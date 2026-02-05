<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Profile;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use App\Models\CalendarSetting;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_slots_returns_closed_days_when_day_is_fully_booked()
    {
        $timezone = 'UTC';
        Carbon::setTestNow(Carbon::create(2024, 5, 20, 10, 0, 0, $timezone)); // Pondelok

        $user = User::factory()->create();
        $profile = Profile::create([
            'owner_id' => $user->id,
            'name' => 'Test Shop',
            'slug' => 'test-shop',
            'timezone' => $timezone,
        ]);

        CalendarSetting::create([
            'profile_id' => $profile->id,
            'slot_interval_minutes' => 60,
            'min_notice_minutes' => 0,
        ]);

        $service = Service::create([
            'profile_id' => $profile->id,
            'name' => 'Test Service',
            'base_duration_minutes' => 60,
        ]);

        $targetDate = Carbon::create(2024, 5, 21, 0, 0, 0, $timezone); // Utorok

        // Pracovná doba 08:00 - 09:00 (iba jeden slot 60 min)
        Schedule::create([
            'profile_id' => $profile->id,
            'day_of_week' => 2, // Utorok
            'start_time' => '08:00',
            'end_time' => '09:00',
        ]);

        $serviceAvailability = app(AvailabilityService::class);

        // Najprv skontrolujeme, že deň nie je closed
        $result = $serviceAvailability->slots($profile, 60, $targetDate, 1);
        $this->assertNotContains($targetDate->toDateString(), $result['closed_days']);
        $this->assertCount(1, $result['slots']);
        $this->assertEquals('available', $result['slots'][0]['status']);

        // Vytvoríme rezerváciu na ten jediný slot
        Appointment::create([
            'profile_id' => $profile->id,
            'service_id' => $service->id,
            'customer_name' => 'Test',
            'start_at' => $targetDate->copy()->setTime(8, 0),
            'end_at' => $targetDate->copy()->setTime(9, 0),
            'status' => 'confirmed',
        ]);

        // Teraz by mal byť deň v closed_days, pretože jediný slot je busy
        $result = $serviceAvailability->slots($profile, 60, $targetDate, 1);
        $this->assertContains($targetDate->toDateString(), $result['closed_days']);
        $this->assertCount(1, $result['slots']);
        $this->assertEquals('busy', $result['slots'][0]['status']);

        Carbon::setTestNow();
    }
}
