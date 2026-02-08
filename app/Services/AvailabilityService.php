<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentLock;
use App\Models\Holiday;
use App\Models\Profile;
use App\Models\Schedule;
use App\Models\ServiceVariant;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Generate available slots for a profile + variant in a date range.
     *
     * @return array<int, array<string, mixed>>
     */
    public function slots(Profile $profile, int $durationMinutes, CarbonInterface $startDate, int $days = 3, ?int $employeeId = null, ?int $variantId = null, int $bufferBefore = 0, int $bufferAfter = 0, ?int $serviceId = null): array
    {
        $timezone = $profile->timezone ?? config('app.timezone');
        $start = $startDate->copy()->startOfDay()->setTimezone($timezone);
        $endRange = $start->copy()->addDays($days)->endOfDay();

        $settings = $profile->calendarSetting;
        $targetService = $serviceId ? $profile->services()->find($serviceId) : null;
        $restrictedServices = $profile->services()
            ->whereNotNull('available_from')
            ->whereNotNull('available_to')
            ->where('is_active', true)
            ->get();

        $slotInterval = max($settings?->slot_interval_minutes ?? 15, 5);
        if ($targetService && $targetService->slot_interval_minutes) {
            $slotInterval = $targetService->slot_interval_minutes;
        }
        $minNotice = $settings?->min_notice_minutes ?? 60;
        $maxAdvance = $settings?->max_advance_days ?? 90;
        $bufferBefore = ($settings?->buffer_before_minutes ?? 0) + $bufferBefore;
        $bufferAfter = ($settings?->buffer_after_minutes ?? 0) + $bufferAfter;
        $serviceDuration = ($durationMinutes ?: 30) + $bufferBefore + $bufferAfter;

        // Identify which employees we should consider
        $employeeIds = [];
        if ($employeeId) {
            $employeeIds = [$employeeId];
        } elseif ($serviceId) {
            $employeeIds = \DB::table('employee_service')
                ->where('service_id', $serviceId)
                ->join('employees', 'employees.id', '=', 'employee_service.employee_id')
                ->where('employees.is_active', true)
                ->pluck('employee_id')
                ->push(null)
                ->toArray();
        }

        // If still no specific employees, we look at all active employees or just the business (null)
        if (empty($employeeIds)) {
            $employeeIds = $profile->employees()->where('is_active', true)->pluck('id')->push(null)->toArray();
        }

        // If there are still no employees, we fallback to business-wide (represented by null)
        if (empty($employeeIds)) {
            $employeeIds = [null];
        }

        $appointments = Appointment::query()
            ->where('profile_id', $profile->id)
            ->whereNotIn('status', ['cancelled'])
            ->whereBetween('start_at', [$start->toDateTimeString(), $endRange->toDateTimeString()])
            ->get();

        $locks = AppointmentLock::query()
            ->where('profile_id', $profile->id)
            ->where('expires_at', '>', Carbon::now($timezone))
            ->whereBetween('start_at', [$start->toDateTimeString(), $endRange->toDateTimeString()])
            ->get();

        $allSchedules = Schedule::query()
            ->with('breaks')
            ->where('profile_id', $profile->id)
            ->where(function ($q) use ($employeeIds) {
                $q->whereIn('employee_id', $employeeIds);
                if (in_array(null, $employeeIds)) {
                    $q->orWhereNull('employee_id');
                }
            })
            ->get();

        $allHolidays = Holiday::query()
            ->where('profile_id', $profile->id)
            ->where(function ($q) use ($employeeIds) {
                $q->whereIn('employee_id', $employeeIds);
                if (in_array(null, $employeeIds)) {
                    $q->orWhereNull('employee_id');
                }
            })
            ->whereBetween('date', [$start->toDateString(), $endRange->toDateString()])
            ->get();

        $slots = [];
        $closedDays = [];
        $now = Carbon::now($timezone);
        $maxAdvanceBoundary = $start->copy()->addDays($maxAdvance ?? 90);

        for ($day = 0; $day < $days; $day++) {
            $date = $start->copy()->addDays($day);
            $dateString = $date->toDateString();
            $hasAnyAvailableSlotOnThisDay = false;

            if ($date->greaterThan($maxAdvanceBoundary)) {
                $closedDays[] = $dateString;
                continue;
            }

            // Generate potential slot times based on profile settings (business hours)
            // or we could generate them for each employee.
            // For now, we use a single timeline but check if ANY employee is free at that time.

            // To find windows, we need to know when the business/employees are working
            $dailyWindows = $this->getWorkingWindowsForEmployees($profile, $employeeIds, $allSchedules, $allHolidays, $date);

            // Flatten all windows for this day to iterate over them
            $mergedWindows = $this->mergeWindows(collect($dailyWindows)->flatten(1)->toArray());

            // If target service has restricted time, we restrict mergedWindows to that window
            if ($targetService && $targetService->available_from && $targetService->available_to) {
                $serviceStart = $date->copy()->setTimeFromTimeString($targetService->available_from);
                $serviceEnd = $date->copy()->setTimeFromTimeString($targetService->available_to);

                $restrictedMergedWindows = [];
                foreach ($mergedWindows as [$wStart, $wEnd]) {
                    $overlapStart = $wStart->gt($serviceStart) ? $wStart : $serviceStart;
                    $overlapEnd = $wEnd->lt($serviceEnd) ? $wEnd : $serviceEnd;

                    if ($overlapStart->lt($overlapEnd)) {
                        $restrictedMergedWindows[] = [$overlapStart, $overlapEnd];
                    }
                }
                $mergedWindows = $restrictedMergedWindows;
            }

            // If ANY OTHER services have restricted time windows, we MUST subtract them from mergedWindows
            // so they are not available for the current target service.
            $otherRestrictedServices = $restrictedServices->filter(fn($rs) => !$targetService || $rs->id !== $targetService->id);
            if ($otherRestrictedServices->isNotEmpty()) {
                $blockedIntervals = [];
                foreach ($otherRestrictedServices as $rs) {
                    $blockedIntervals[] = [
                        $date->copy()->setTimeFromTimeString($rs->available_from),
                        $date->copy()->setTimeFromTimeString($rs->available_to)
                    ];
                }
                $mergedWindows = $this->subtractIntervals($mergedWindows, $blockedIntervals);
            }

            foreach ($mergedWindows as [$windowStart, $windowEnd]) {
                for ($slotStart = $windowStart->copy(); $slotStart->lt($windowEnd); $slotStart->addMinutes($slotInterval)) {
                    $slotEnd = $slotStart->copy()->addMinutes($serviceDuration);

                    if ($slotEnd->greaterThan($windowEnd)) {
                        break;
                    }

                    if ($slotStart->lessThan($now->copy()->addMinutes($minNotice))) {
                        continue;
                    }

                    // Check status across all employees
                    $status = $this->getAggregateStatus($slotStart, $slotEnd, $employeeIds, $appointments, $locks, $dailyWindows);

                    if ($status === 'available') {
                        $hasAnyAvailableSlotOnThisDay = true;
                    }

                    // Only add available or locking slots (hide busy ones)
                    if ($status === 'available' || $status === 'locking') {
                        $slots[] = [
                            'profile_id' => $profile->id,
                            'service_variant_id' => $variantId,
                            'employee_id' => $employeeId, // Keep original requested employee_id if any
                            'start_at' => $slotStart->toIso8601String(),
                            'end_at' => $slotEnd->toIso8601String(),
                            'status' => $status,
                        ];
                    }
                }
            }

            if (! $hasAnyAvailableSlotOnThisDay && ! in_array($dateString, $closedDays)) {
                $closedDays[] = $dateString;
            }
        }

        return [
            'slots' => $slots,
            'closed_days' => array_unique($closedDays),
        ];
    }

    protected function getWorkingWindowsForEmployees(Profile $profile, array $employeeIds, Collection $allSchedules, Collection $allHolidays, CarbonInterface $date): array
    {
        $timezone = $profile->timezone ?? config('app.timezone');
        $windowsPerEmployee = [];

        foreach ($employeeIds as $empId) {
            $empSchedules = $allSchedules->filter(fn($s) => $s->employee_id == $empId);
            $empHolidays = $allHolidays->filter(fn($h) => $h->employee_id == $empId);

            if ($this->isClosedByHoliday($empHolidays, $date)) {
                $windowsPerEmployee[$empId] = [];
                continue;
            }

            $daySchedules = $this->schedulesForDay($empSchedules, $date);
            $blockedIntervals = $this->holidayIntervals($empHolidays, $date, $timezone);

            $empWindows = [];
            foreach ($daySchedules as $schedule) {
                $scheduleStart = Carbon::parse($schedule->start_time, $timezone)->setDate($date->year, $date->month, $date->day);
                $scheduleEnd = Carbon::parse($schedule->end_time, $timezone)->setDate($date->year, $date->month, $date->day);

                if ($scheduleStart->greaterThanOrEqualTo($scheduleEnd)) continue;

                $windows = $this->subtractIntervals([[$scheduleStart, $scheduleEnd]], $this->breakIntervals($schedule, $date, $timezone));
                $windows = $this->subtractIntervals($windows, $blockedIntervals);

                foreach ($windows as $window) {
                    $empWindows[] = $window;
                }
            }
            $windowsPerEmployee[$empId] = $empWindows;
        }

        return $windowsPerEmployee;
    }

    protected function mergeWindows(array $windows): array
    {
        if (empty($windows)) return [];

        // Sort by start time
        usort($windows, fn($a, $b) => $a[0]->timestamp <=> $b[0]->timestamp);

        $merged = [];
        $current = $windows[0];

        for ($i = 1; $i < count($windows); $i++) {
            $next = $windows[$i];
            if ($next[0]->lessThanOrEqualTo($current[1])) {
                if ($next[1]->greaterThan($current[1])) {
                    $current[1] = $next[1];
                }
            } else {
                $merged[] = $current;
                $current = $next;
            }
        }
        $merged[] = $current;

        return $merged;
    }

    protected function getAggregateStatus(CarbonInterface $start, CarbonInterface $end, array $employeeIds, Collection $appointments, Collection $locks, array $dailyWindows): string
    {
        $statuses = [];

        foreach ($employeeIds as $empId) {
            // Check if employee is working during this slot
            $isWorking = false;
            foreach ($dailyWindows[$empId] ?? [] as [$winStart, $winEnd]) {
                if ($start->greaterThanOrEqualTo($winStart) && $end->lessThanOrEqualTo($winEnd)) {
                    $isWorking = true;
                    break;
                }
            }

            if (!$isWorking) {
                $statuses[] = 'busy';
                continue;
            }

            $statuses[] = $this->getIndividualStatus($start, $end, $empId, $appointments, $locks);
        }

        if (in_array('available', $statuses)) return 'available';
        if (in_array('locking', $statuses)) return 'locking';
        return 'busy';
    }

    protected function getIndividualStatus(CarbonInterface $start, CarbonInterface $end, $employeeId, Collection $appointments, Collection $locks): string
    {
        $overlap = fn ($itemStart, $itemEnd) => $itemStart->lt($end) && $itemEnd->gt($start);

        $appointmentConflict = $appointments->first(function (Appointment $appointment) use ($overlap, $employeeId) {
            if ($employeeId) {
                // If we check for specific employee, skip appointments for other specific employees
                if ($appointment->employee_id && (int) $appointment->employee_id !== (int) $employeeId) {
                    return false;
                }
                // If appointment has NO employee, it blocks everyone?
                // Usually yes, if it's a profile-level appointment.
            } else {
                // If we are checking for "business-level" (null), only appointments with no employee block it
                if ($appointment->employee_id) {
                    return false;
                }
            }

            return $overlap($appointment->start_at, $appointment->end_at);
        });

        if ($appointmentConflict) return 'busy';

        $lockConflict = $locks->first(function (AppointmentLock $lock) use ($overlap, $employeeId) {
            if ($employeeId) {
                if ($lock->employee_id && (int) $lock->employee_id !== (int) $employeeId) {
                    return false;
                }
            } else {
                if ($lock->employee_id) {
                    return false;
                }
            }

            return $overlap($lock->start_at, $lock->end_at);
        });

        if ($lockConflict) return 'locking';

        return 'available';
    }

    /**
     * Get working windows for a profile in a date range.
     * These are time intervals when the business is open, excluding breaks and holidays.
     *
     * @return array<string, array<int, array{0: CarbonInterface, 1: CarbonInterface}>>
     */
    public function getWorkingWindows(Profile $profile, CarbonInterface $startDate, int $days = 1, ?int $employeeId = null): array
    {
        $timezone = $profile->timezone ?? config('app.timezone');
        $start = $startDate->copy()->startOfDay()->setTimezone($timezone);
        $endRange = $start->copy()->addDays($days)->endOfDay();

        $employeeIds = $employeeId ? [$employeeId] : $profile->employees()->where('is_active', true)->pluck('id')->push(null)->toArray();

        $allSchedules = Schedule::query()
            ->with('breaks')
            ->where('profile_id', $profile->id)
            ->where(function ($q) use ($employeeIds) {
                $q->whereIn('employee_id', $employeeIds);
                if (in_array(null, $employeeIds)) {
                    $q->orWhereNull('employee_id');
                }
            })
            ->get();

        $allHolidays = Holiday::query()
            ->where('profile_id', $profile->id)
            ->where(function ($q) use ($employeeIds) {
                $q->whereIn('employee_id', $employeeIds);
                if (in_array(null, $employeeIds)) {
                    $q->orWhereNull('employee_id');
                }
            })
            ->whereBetween('date', [$start->toDateString(), $endRange->toDateString()])
            ->get();

        $dailyWindows = [];

        for ($day = 0; $day < $days; $day++) {
            $date = $start->copy()->addDays($day);
            $dateString = $date->toDateString();

            $empWindows = $this->getWorkingWindowsForEmployees($profile, $employeeIds, $allSchedules, $allHolidays, $date);
            $merged = $this->mergeWindows(collect($empWindows)->flatten(1)->toArray());

            $dailyWindows[$dateString] = $merged;
        }

        return $dailyWindows;
    }

    protected function schedulesForDay(Collection $schedules, CarbonInterface $date): Collection
    {
        return $schedules->filter(function (Schedule $schedule) use ($date) {
            if ((int) $schedule->day_of_week !== (int) $date->dayOfWeek) {
                return false;
            }

            if ($schedule->effective_from && $date->lt(Carbon::parse($schedule->effective_from))) {
                return false;
            }

            if ($schedule->effective_to && $date->gt(Carbon::parse($schedule->effective_to))) {
                return false;
            }

            return true;
        });
    }

    protected function breakIntervals(Schedule $schedule, CarbonInterface $date, string $timezone): array
    {
        return $schedule->breaks->map(function ($break) use ($date, $timezone) {
            $start = Carbon::parse($break->start_time, $timezone)->setDate($date->year, $date->month, $date->day);
            $end = Carbon::parse($break->end_time, $timezone)->setDate($date->year, $date->month, $date->day);

            return [$start, $end];
        })->all();
    }

    protected function holidayIntervals(Collection $holidays, CarbonInterface $date, string $timezone): array
    {
        return $holidays->filter(fn (Holiday $holiday) => $holiday->date->isSameDay($date))
            ->filter(fn (Holiday $holiday) => $holiday->start_time && $holiday->end_time)
            ->map(function (Holiday $holiday) use ($timezone) {
                $start = Carbon::parse($holiday->start_time, $timezone)->setDate($holiday->date->year, $holiday->date->month, $holiday->date->day);
                $end = Carbon::parse($holiday->end_time, $timezone)->setDate($holiday->date->year, $holiday->date->month, $holiday->date->day);
                return [$start, $end];
            })
            ->values()
            ->all();
    }

    protected function isClosedByHoliday(Collection $holidays, CarbonInterface $date): bool
    {
        return $holidays
            ->first(fn (Holiday $holiday) => $holiday->date->isSameDay($date) && $holiday->is_closed && ! $holiday->start_time && ! $holiday->end_time) !== null;
    }

    /**
     * Subtract blocked intervals from available windows.
     *
     * @param  array<int, array{0: CarbonInterface, 1: CarbonInterface}>  $windows
     * @param  array<int, array{0: CarbonInterface, 1: CarbonInterface}>  $blocked
     * @return array<int, array{0: CarbonInterface, 1: CarbonInterface}>
     */
    protected function subtractIntervals(array $windows, array $blocked): array
    {
        foreach ($blocked as [$blockStart, $blockEnd]) {
            $next = [];

            foreach ($windows as [$winStart, $winEnd]) {
                if ($blockEnd->lessThanOrEqualTo($winStart) || $blockStart->greaterThanOrEqualTo($winEnd)) {
                    $next[] = [$winStart, $winEnd];
                    continue;
                }

                if ($blockStart->greaterThan($winStart)) {
                    $next[] = [$winStart, $blockStart];
                }

                if ($blockEnd->lessThan($winEnd)) {
                    $next[] = [$blockEnd, $winEnd];
                }
            }

            $windows = $next;
        }

        return $windows;
    }

    protected function getStatus(CarbonInterface $start, CarbonInterface $end, Collection $appointments, Collection $locks, ?int $employeeId): string
    {
        $overlap = fn ($itemStart, $itemEnd) => $itemStart->lt($end) && $itemEnd->gt($start);

        $appointmentConflict = $appointments->first(function (Appointment $appointment) use ($overlap, $employeeId) {
            if ($employeeId) {
                if ($appointment->employee_id && (int) $appointment->employee_id !== (int) $employeeId) {
                    return false;
                }
            } else {
                if ($appointment->employee_id) {
                    return false;
                }
            }

            return $overlap($appointment->start_at, $appointment->end_at);
        });

        if ($appointmentConflict) {
            return 'busy';
        }

        $lockConflict = $locks->first(function (AppointmentLock $lock) use ($overlap, $employeeId) {
            if ($employeeId) {
                if ($lock->employee_id && (int) $lock->employee_id !== (int) $employeeId) {
                    return false;
                }
            } else {
                if ($lock->employee_id) {
                    return false;
                }
            }

            return $overlap($lock->start_at, $lock->end_at);
        });

        if ($lockConflict) {
            return 'locking';
        }

        return 'available';
    }
}
