<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Services\AvailabilityService;
use App\Services\PakavozService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AvailabilityController extends Controller
{
    public function __construct(
        private AvailabilityService $availability,
        private PakavozService $pakavoz
    ) {
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'profile_id' => ['required', 'exists:profiles,id'],
            'service_variant_id' => ['nullable', 'exists:service_variants,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'days' => ['nullable', 'integer', 'min:1', 'max:14'],
        ]);

        $profile = Profile::findOrFail($validated['profile_id']);
        $variant = null;
        $duration = null;
        $serviceId = $validated['service_id'];
        $bufferBefore = 0;
        $bufferAfter = 0;
        $currency = null;
        $service = null;

        if (! empty($validated['service_variant_id'])) {
            $variant = ServiceVariant::with('service.profile.calendarSetting')->findOrFail($validated['service_variant_id']);
            $service = $variant->service;
            $serviceId = $service->id;
            $bufferBefore = $variant->buffer_before_minutes ?? 0;
            $bufferAfter = $variant->buffer_after_minutes ?? 0;
            $currency = $variant->currency;
            // variant je doplnok k základnej službe, pripočítaj jeho čas
            $duration = ($service->base_duration_minutes ?? 0) + ($variant->duration_minutes ?? 0);
        } else {
            $service = Service::findOrFail($validated['service_id']);
            $currency = $service->currency;
            $duration = $service->base_duration_minutes;
        }

        $timezone = $profile?->timezone ?? config('app.timezone');
        $startDate = Carbon::parse($validated['date'], $timezone)->startOfDay();
        $days = $validated['days'] ?? 3;
        $now = Carbon::now($timezone);
        $settings = $profile->calendarSetting;
        $minNotice = $settings?->min_notice_minutes ?? 60;

        if ($profile->isApiAvailable('pakavoz') && $service->is_pakavoz_enabled && $service->pakavoz_api_key) {
            $slots = [];
            $closedDays = [];
            $dailyWindows = $this->availability->getWorkingWindows($profile, $startDate, $days, $validated['employee_id'] ?? null);

            for ($i = 0; $i < $days; $i++) {
                $currentDate = $startDate->copy()->addDays($i);
                $dateString = $currentDate->toDateString();
                $windows = $dailyWindows[$dateString] ?? [];

                if (empty($windows)) {
                    $closedDays[] = $dateString;
                    continue;
                }

                $response = $this->pakavoz->getAvailability($service->pakavoz_api_key, $dateString);
                $hasAnyAvailableSlotOnThisDay = false;

                foreach ($response['availability'] ?? [] as $slot) {
                    if (! ($slot['is_full'] ?? false) && ($slot['available_slots'] ?? 0) > 0) {
                        $slotStart = Carbon::parse($dateString.' '.$slot['time'], $timezone);
                        $slotEnd = $slotStart->copy()->addMinutes($duration);

                        // Overenie, či slot nie je v minulosti (zohľadnenie min_notice)
                        if ($slotStart->lessThan($now->copy()->addMinutes($minNotice))) {
                            continue;
                        }

                        // Overenie, či slot spadá do otváracích hodín prevádzky
                        $isInWindow = false;
                        foreach ($windows as [$winStart, $winEnd]) {
                            if ($slotStart->greaterThanOrEqualTo($winStart) && $slotEnd->lessThanOrEqualTo($winEnd)) {
                                $isInWindow = true;
                                break;
                            }
                        }

                        if (! $isInWindow) {
                            continue;
                        }

                        $hasAnyAvailableSlotOnThisDay = true;
                        $slots[] = [
                            'profile_id' => $profile->id,
                            'service_variant_id' => $variant?->id,
                            'employee_id' => $validated['employee_id'] ?? null,
                            'start_at' => $slotStart->toIso8601String(),
                            'end_at' => $slotEnd->toIso8601String(),
                            'status' => 'available',
                        ];
                    }
                }

                if (! $hasAnyAvailableSlotOnThisDay) {
                    $closedDays[] = $dateString;
                }
            }

            return response()->json([
                'profile_id' => $profile->id,
                'service_id' => $serviceId,
                'service_variant_id' => $variant?->id,
                'currency' => $currency,
                'slots' => $slots,
                'closed_days' => array_values(array_unique($closedDays)),
            ]);
        }

        $result = $this->availability->slots(
            $profile,
            $duration,
            $startDate,
            $days,
            $validated['employee_id'] ?? null,
            $variant?->id,
            $bufferBefore,
            $bufferAfter
        );

        return response()->json([
            'profile_id' => $profile?->id,
            'service_id' => $serviceId,
            'service_variant_id' => $variant?->id,
            'currency' => $currency,
            'slots' => $result['slots'],
            'closed_days' => $result['closed_days'],
        ]);
    }
}
