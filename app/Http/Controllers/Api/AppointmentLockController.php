<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentLock;
use App\Models\Profile;
use App\Models\Service;
use App\Models\ServiceVariant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppointmentLockController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'service_id' => ['required', 'exists:services,id'],
            'service_variant_id' => ['nullable', 'exists:service_variants,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'start_at' => ['required', 'string'],
            'date' => ['nullable', 'date'],
        ]);

        $profile = Profile::findOrFail($validated['profile_id']);
        $service = Service::findOrFail($validated['service_id']);
        $variant = $validated['service_variant_id'] ? ServiceVariant::findOrFail($validated['service_variant_id']) : null;

        $timezone = $profile->timezone ?? config('app.timezone');

        // Parse start time
        if ($validated['date'] && preg_match('/^\d{1,2}:\d{2}$/', $validated['start_at'])) {
            $startAt = Carbon::createFromFormat('Y-m-d H:i', "{$validated['date']} {$validated['start_at']}", $timezone);
        } else {
            $startAt = Carbon::parse($validated['start_at'], $timezone);
        }

        $settings = $profile->calendarSetting;
        $variantBufferBefore = $variant?->buffer_before_minutes ?? 0;
        $variantBufferAfter = $variant?->buffer_after_minutes ?? 0;
        $bufferBefore = ($settings?->buffer_before_minutes ?? 0) + $variantBufferBefore;
        $bufferAfter = ($settings?->buffer_after_minutes ?? 0) + $variantBufferAfter;
        // Variant má vlastný čas (ak nie je definovaný, použije sa základný)
        $baseDuration = $variant ? ($variant->duration_minutes ?? $service->base_duration_minutes) : ($service->base_duration_minutes ?? 30);
        $duration = $baseDuration + $bufferBefore + $bufferAfter;
        $endAt = $startAt->copy()->addMinutes($duration);

        // Check if already locked by someone else or if there's an appointment
        // This is a simplified check, full check is in AvailabilityService
        // But here we need to create the lock.

        $lock = AppointmentLock::create([
            'profile_id' => $profile->id,
            'employee_id' => $validated['employee_id'] ?? null,
            'service_id' => $service->id,
            'service_variant_id' => $variant?->id,
            'token' => (string) Str::uuid(),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'expires_at' => Carbon::now($timezone)->addMinutes(5),
        ]);

        return response()->json([
            'message' => 'Slot bol dočasne zablokovaný na 5 minút.',
            'token' => $lock->token,
            'expires_at' => $lock->expires_at->toIso8601String(),
        ], 201);
    }
}
