<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentLock;
use App\Models\Service;
use App\Models\ServiceVariant;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(private PakavozService $pakavoz)
    {
    }

    public function book(array $data): Appointment
    {
        $variant = null;
        $service = Service::with('profile.calendarSetting')->findOrFail($data['service_id']);
        $profile = $service->profile;
        $settings = $profile->calendarSetting;

        if (! empty($data['service_variant_id'])) {
            $variant = ServiceVariant::with(['service.profile.calendarSetting'])->findOrFail($data['service_variant_id']);
            if ((int) $variant->service_id !== (int) $service->id) {
                throw ValidationException::withMessages([
                    'service_variant_id' => 'Variant nepatrí k službe.',
                ]);
            }
        }

        $timezone = $profile->timezone ?? config('app.timezone');
        $startAt = $this->parseStartAt($data['start_at'], $data['date'] ?? null, $timezone);
        $variantBufferBefore = $variant?->buffer_before_minutes ?? 0;
        $variantBufferAfter = $variant?->buffer_after_minutes ?? 0;
        $bufferBefore = ($settings?->buffer_before_minutes ?? 0) + $variantBufferBefore;
        $bufferAfter = ($settings?->buffer_after_minutes ?? 0) + $variantBufferAfter;
        // Variant slúži ako doplnok k základnej službe, takže čas = základ + variant
        $baseDuration = ($service->base_duration_minutes ?? 30) + ($variant?->duration_minutes ?? 0);
        $duration = $baseDuration + $bufferBefore + $bufferAfter;
        $endAt = $startAt->copy()->addMinutes($duration);

        $minNotice = $settings?->min_notice_minutes ?? 60;
        if ($startAt->lt(Carbon::now($timezone)->addMinutes($minNotice))) {
            throw ValidationException::withMessages([
                'start_at' => 'Termín je príliš blízko, vyber neskorší čas.',
            ]);
        }

        $maxAdvance = $settings?->max_advance_days ?? 90;
        if ($startAt->gt(Carbon::now($timezone)->addDays($maxAdvance))) {
            throw ValidationException::withMessages([
                'start_at' => 'Termín je mimo povoleného obdobia rezervácií.',
            ]);
        }

        if ((int) $data['profile_id'] !== (int) $profile->id) {
            throw ValidationException::withMessages([
                'profile_id' => 'Variant služby nepatrí do tejto prevádzky.',
            ]);
        }

        $employeeId = $data['employee_id'] ?? null;
        $requiresConfirmation = (bool) ($settings?->requires_confirmation ?? false);

        if ($service->is_pakavoz_enabled) {
            $errors = [];
            if (empty($data['evc'])) {
                $errors['evc'] = 'Evidenčné číslo vozidla (EČV) je povinné pre túto službu.';
            }
            if (empty($data['customer_name'])) {
                $errors['customer_name'] = 'Meno je povinné pre túto službu.';
            }
            if (empty($data['customer_phone'])) {
                $errors['customer_phone'] = 'Telefónne číslo je povinné pre túto službu.';
            }

            if (! empty($errors)) {
                throw ValidationException::withMessages($errors);
            }
        }

        return DB::transaction(function () use ($data, $variant, $service, $profile, $startAt, $endAt, $duration, $employeeId, $timezone, $requiresConfirmation) {
            $hasAppointments = Appointment::query()
                ->where('profile_id', $profile->id)
                ->whereNotIn('status', ['cancelled'])
                ->when($employeeId, function ($query) use ($employeeId) {
                    $query->where(function ($q) use ($employeeId) {
                        $q->whereNull('employee_id')->orWhere('employee_id', $employeeId);
                    });
                })
                ->when(is_null($employeeId), function ($query) {
                    $query->whereNull('employee_id');
                })
                ->where(function ($query) use ($startAt, $endAt) {
                    $query->where('start_at', '<', $endAt)
                        ->where('end_at', '>', $startAt);
                })
                ->lockForUpdate()
                ->exists();

            if ($hasAppointments) {
                throw ValidationException::withMessages([
                    'start_at' => 'Slot je už obsadený.',
                ]);
            }

            $hasLocks = AppointmentLock::query()
                ->where('profile_id', $profile->id)
                ->where('expires_at', '>', Carbon::now($timezone))
                ->when($employeeId, function ($query) use ($employeeId) {
                    $query->where(function ($q) use ($employeeId) {
                        $q->whereNull('employee_id')->orWhere('employee_id', $employeeId);
                    });
                })
                ->when(is_null($employeeId), function ($query) {
                    $query->whereNull('employee_id');
                })
                ->where(function ($query) use ($startAt, $endAt) {
                    $query->where('start_at', '<', $endAt)
                        ->where('end_at', '>', $startAt);
                })
                ->lockForUpdate()
                ->exists();

            if ($hasLocks) {
                throw ValidationException::withMessages([
                    'start_at' => 'Slot sa práve potvrdzuje iným klientom.',
                ]);
            }

            $appointment = Appointment::create([
                'profile_id' => $profile->id,
                'service_id' => $service->id,
                'service_variant_id' => $variant?->id,
                'employee_id' => $employeeId,
                'user_id' => $data['user_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => $requiresConfirmation ? 'pending' : 'confirmed',
                'price' => ($service->base_price ?? 0) + ($variant?->price ?? 0),
                'currency' => $service->currency ?? $variant?->currency,
                'confirmation_code' => (string) Str::uuid(),
                'metadata' => [
                    'notes' => $data['notes'] ?? null,
                    'duration_with_buffers' => $duration,
                    'evc' => $data['evc'] ?? null,
                    'vehicle_model' => $data['vehicle_model'] ?? null,
                ],
            ]);

            if ($service->is_pakavoz_enabled && $service->pakavoz_api_key) {
                $pakavozResult = $this->pakavoz->createReservation($service->pakavoz_api_key, [
                    'name' => $data['customer_name'],
                    'phone' => $data['customer_phone'],
                    'email' => $data['customer_email'] ?? null,
                    'evc' => $data['evc'],
                    'date' => $startAt->toDateString(),
                    'time' => $startAt->format('H:i'),
                    'model' => $data['vehicle_model'] ?? null,
                    'note' => $data['notes'] ?? null,
                ]);

                if (! $pakavozResult['success']) {
                    throw ValidationException::withMessages([
                        'start_at' => $pakavozResult['message'],
                    ]);
                }

                $metadata = $appointment->metadata;
                $metadata['pakavoz_reservation_id'] = $pakavozResult['data']['reservation_id'] ?? null;
                $appointment->update(['metadata' => $metadata]);
            }

            // Lock slot for a few minutes to prevent race during post-processing (notifications, payment intent, etc.)
            AppointmentLock::create([
                'profile_id' => $profile->id,
                'employee_id' => $employeeId,
                'service_id' => $service->id,
                'service_variant_id' => $variant?->id,
                'user_id' => $data['user_id'] ?? null,
                'token' => (string) Str::uuid(),
                'start_at' => $startAt,
                'end_at' => $endAt,
                'expires_at' => Carbon::now($timezone)->addMinutes(5),
            ]);

            return $appointment;
        });
    }

    protected function parseStartAt(string $value, ?string $date, string $timezone): CarbonInterface
    {
        // If we only got time and a separate date, merge them.
        if ($date && preg_match('/^\\d{1,2}:\\d{2}$/', $value)) {
            return Carbon::createFromFormat('Y-m-d H:i', "{$date} {$value}", $timezone);
        }

        return Carbon::parse($value, $timezone);
    }
}
