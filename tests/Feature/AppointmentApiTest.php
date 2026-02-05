<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function createPakavozService()
    {
        $user = User::factory()->create();
        $profile = Profile::create([
            'owner_id' => $user->id,
            'name' => 'Test Shop',
            'slug' => 'test-shop',
            'email' => 'test@example.com',
            'timezone' => 'UTC',
        ]);

        for ($i = 0; $i < 7; $i++) {
            Schedule::create([
                'profile_id' => $profile->id,
                'day_of_week' => $i,
                'start_time' => '08:00',
                'end_time' => '20:00',
            ]);
        }

        $service = Service::create([
            'profile_id' => $profile->id,
            'name' => 'STK Kontrola',
            'base_duration_minutes' => 30,
            'is_pakavoz_enabled' => true,
            'pakavoz_api_key' => 'test-key',
            'is_active' => true,
        ]);

        return [$profile, $service];
    }

    public function test_appointment_store_works_with_profile_id()
    {
        [$profile, $service] = $this->createPakavozService();
        $startAt = now()->addDay()->setTime(10, 0, 0);

        Http::fake([
            'pakavoz.sk/api/v1/reservation' => Http::response(['message' => 'Rezervácia bola úspešne vytvorená.', 'reservation_id' => 1234], 201),
        ]);

        $response = $this->postJson('/api/appointments', [
            'profile_id' => $profile->id,
            'service_id' => $service->id,
            'start_at' => $startAt->toISOString(),
            'customer_name' => 'Jan Novak',
            'customer_email' => 'jan@example.com',
            'customer_phone' => '+421900111222',
            'evc' => 'BA123XY',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('appointments', [
            'profile_id' => $profile->id,
            'customer_name' => 'Jan Novak',
        ]);
    }

    public function test_appointment_store_fails_with_shop_id_instead_of_profile_id()
    {
        [$profile, $service] = $this->createPakavozService();
        $startAt = now()->addDay()->setTime(10, 0, 0);

        $response = $this->postJson('/api/appointments', [
            'shop_id' => $profile->id, // Nesprávny kľúč
            'service_id' => $service->id,
            'start_at' => $startAt->toISOString(),
            'customer_name' => 'Jan Novak',
            'customer_email' => 'jan@example.com',
            'customer_phone' => '+421900111222',
            'evc' => 'BA123XY',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['profile_id']);
    }

    public function test_appointment_store_fails_without_customer_phone_for_pakavoz()
    {
        [$profile, $service] = $this->createPakavozService();
        $startAt = now()->addDay()->setTime(10, 0, 0);

        $response = $this->postJson('/api/appointments', [
            'profile_id' => $profile->id,
            'service_id' => $service->id,
            'start_at' => $startAt->toISOString(),
            'customer_name' => 'Jan Novak',
            'customer_email' => 'jan@example.com',
            // customer_phone chýba
            'evc' => 'BA123XY',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_phone']);
    }

    public function test_appointment_store_works_with_lock_token()
    {
        [$profile, $service] = $this->createPakavozService();
        $timezone = $profile->timezone;
        $startAt = Carbon::now($timezone)->addDay()->setTime(10, 0, 0);

        // Vytvoríme zámok
        $lockToken = 'test-lock-token';
        \App\Models\AppointmentLock::create([
            'profile_id' => $profile->id,
            'service_id' => $service->id,
            'token' => $lockToken,
            'start_at' => $startAt,
            'end_at' => $startAt->copy()->addMinutes(30),
            'expires_at' => Carbon::now($timezone)->addMinutes(5),
        ]);

        Http::fake([
            'pakavoz.sk/api/v1/reservation' => Http::response(['message' => 'Rezervácia bola úspešne vytvorená.', 'reservation_id' => 1234], 201),
        ]);

        // Pokus o rezerváciu BEZ tokenu by mal zlyhať
        $response = $this->postJson('/api/appointments', [
            'profile_id' => $profile->id,
            'service_id' => $service->id,
            'start_at' => $startAt->toIso8601String(),
            'customer_name' => 'Jan Novak',
            'customer_email' => 'jan@example.com',
            'customer_phone' => '+421900111222',
            'evc' => 'BA123XY',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['start_at' => ['Slot sa práve potvrdzuje iným klientom.']]);

        // Pokus o rezerváciu S tokenom by mal prejsť
        $response = $this->postJson('/api/appointments', [
            'profile_id' => $profile->id,
            'service_id' => $service->id,
            'start_at' => $startAt->toISOString(),
            'customer_name' => 'Jan Novak',
            'customer_email' => 'jan@example.com',
            'customer_phone' => '+421900111222',
            'evc' => 'BA123XY',
            'lock_token' => $lockToken,
        ]);

        $response->assertStatus(201);
    }
}
