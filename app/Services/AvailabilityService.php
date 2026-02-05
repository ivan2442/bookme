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
    public function slots(Profile $profile, int $durationMinutes, CarbonInterface $startDate, int $days = 3, ?int $employeeId = null, ?int $variantId = null, int $bufferBefore = 0, int $bufferAfter = 0): array
    {
        $timezone = $profile->timezone ?? config('app.timezone');
        $start = $startDate->copy()->startOfDay()->setTimezone($timezone);
        $endRange = $start->copy()->addDays($days)->endOfDay();

        $settings = $profile->calendarSetting;
        $slotInterval = max($settings?->slot_interval_minutes ?? 15, 5);
        $minNotice = $settings?->min_notice_minutes ?? 60;
        $maxAdvance = $settings?->max_advance_days ?? 90;
        $bufferBefore = ($settings?->buffer_before_minutes ?? 0) + $bufferBefore;
        $bufferAfter = ($settings?->buffer_after_minutes ?? 0) + $bufferAfter;
        $serviceDuration = ($durationMinutes ?: 30) + $bufferBefore + $bufferAfter;

        $appointments = Appointment::query()
            ->where('profile_id', $profile->id)
            ->whereNotIn('status', ['cancelled'])
            ->whereBetween('start_at', [$start->toDateTimeString(), $endRange->toDateTimeString()])
            ->when($employeeId, function ($query) use ($employeeId) {
                $query->where(function ($q) use ($employeeId) {
                    $q->whereNull('employee_id')->orWhere('employee_id', $employeeId);
                });
            })
            ->get();

        $locks = AppointmentLock::query()
            ->where('profile_id', $profile->id)
            ->where('expires_at', '>', Carbon::now($timezone))
            ->whereBetween('start_at', [$start->toDateTimeString(), $endRange->toDateTimeString()])
            ->when($employeeId, function ($query) use ($employeeId) {
                $query->where(function ($q) use ($employeeId) {
                    $q->whereNull('employee_id')->orWhere('employee_id', $employeeId);
                });
            })
            ->get();

        $dailyWindows = $this->getWorkingWindows($profile, $startDate, $days, $employeeId);
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

            $windows = $dailyWindows[$dateString] ?? [];
            if (empty($windows)) {
                $closedDays[] = $dateString;
                continue;
            }

            foreach ($windows as [$windowStart, $windowEnd]) {
                for ($slotStart = $windowStart->copy(); $slotStart->lt($windowEnd); $slotStart->addMinutes($slotInterval)) {
                    $slotEnd = $slotStart->copy()->addMinutes($serviceDuration);

                    if ($slotEnd->greaterThan($windowEnd)) {
                        break;
                    }

                    if ($slotStart->lessThan($now->copy()->addMinutes($minNotice))) {
                        continue;
                    }

                    $status = $this->getStatus($slotStart, $slotEnd, $appointments, $locks, $employeeId);

                    if ($status === 'available') {
                        $hasAnyAvailableSlotOnThisDay = true;
                    }

                    $slots[] = [
                        'profile_id' => $profile->id,
                        'service_variant_id' => $variantId,
                        'employee_id' => $employeeId,
                        'start_at' => $slotStart->toIso8601String(),
                        'end_at' => $slotEnd->toIso8601String(),
                        'status' => $status,
                    ];
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

        $schedules = Schedule::query()
            ->with('breaks')
            ->where('profile_id', $profile->id)
            ->when($employeeId, function ($query) use ($employeeId) {
                $query->where(function ($q) use ($employeeId) {
                    $q->whereNull('employee_id')
                        ->orWhere('employee_id', $employeeId);
                });
            })
            ->get();

        $holidays = Holiday::query()
            ->where('profile_id', $profile->id)
            ->when($employeeId, function ($query) use ($employeeId) {
                $query->where(function ($q) use ($employeeId) {
                    $q->whereNull('employee_id')->orWhere('employee_id', $employeeId);
                });
            })
            ->whereBetween('date', [$start->toDateString(), $endRange->toDateString()])
            ->get();

        $dailyWindows = [];

        for ($day = 0; $day < $days; $day++) {
            $date = $start->copy()->addDays($day);
            $dateString = $date->toDateString();
            $dailyWindows[$dateString] = [];

            if ($this->isClosedByHoliday($holidays, $date)) {
                continue;
            }

            $daySchedules = $this->schedulesForDay($schedules, $date);
            if ($daySchedules->isEmpty()) {
                continue;
            }

            $blockedIntervals = $this->holidayIntervals($holidays, $date, $timezone);

            foreach ($daySchedules as $schedule) {
                $scheduleStart = Carbon::parse($schedule->start_time, $timezone)->setDate($date->year, $date->month, $date->day);
                $scheduleEnd = Carbon::parse($schedule->end_time, $timezone)->setDate($date->year, $date->month, $date->day);

                if ($scheduleStart->greaterThanOrEqualTo($scheduleEnd)) {
                    continue;
                }

                $windows = $this->subtractIntervals([[$scheduleStart, $scheduleEnd]], $this->breakIntervals($schedule, $date, $timezone));
                $windows = $this->subtractIntervals($windows, $blockedIntervals);

                foreach ($windows as $window) {
                    $dailyWindows[$dateString][] = $window;
                }
            }
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
