<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PakavozIntegrationTest extends TestCase
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

    public function test_availability_uses_pakavoz_api_when_enabled()
    {
        $targetDate = now()->addDays(10)->format('Y-m-d');
        Http::fake([
            'pakavoz.sk/api/v1/availability*' => Http::response([
                'date' => $targetDate,
                'availability' => [
                    [
                        'time' => '08:00',
                        'available_slots' => 2,
                        'total_slots' => 3,
                        'is_full' => false
                    ],
                ]
            ], 200),
        ]);

        [$profile, $service] = $this->createPakavozService();

        $response = $this->postJson('/api/availability', [
            'service_id' => $service->id,
            'profile_id' => $profile->id,
            'date' => $targetDate,
            'days' => 1
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'slots');
        $this->assertStringContainsString($targetDate.'T08:00:00', $response->json('slots.0.start_at'));
    }

    public function test_reservation_is_sent_to_pakavoz_when_enabled()
    {
        $targetDate = now()->addDays(10)->format('Y-m-d');
        Http::fake([
            'pakavoz.sk/api/v1/reservation' => Http::response([
                'message' => 'Rezervácia bola úspešne vytvorená.',
                'reservation_id' => 1234
            ], 201),
        ]);

        [$profile, $service] = $this->createPakavozService();

        $response = $this->postJson('/api/appointments', [
            'profile_id' => $profile->id,
            'service_id' => $service->id,
            'start_at' => $targetDate . ' 08:00',
            'customer_name' => 'Ján Novák',
            'customer_phone' => '+421900111222',
            'evc' => 'BA123XY',
            'vehicle_model' => 'Škoda Octavia'
        ]);

        $response->assertStatus(201);
        $this->assertEquals(1234, $response->json('metadata.pakavoz_reservation_id'));

        Http::assertSent(function ($request) use ($targetDate) {
            return $request->url() === 'https://pakavoz.sk/api/v1/reservation' &&
                   $request['evc'] === 'BA123XY' &&
                   $request['date'] === $targetDate;
        });
    }

    public function test_reservation_fails_if_pakavoz_returns_error()
    {
        $targetDate = now()->addDays(10)->format('Y-m-d');
        Http::fake([
            'pakavoz.sk/api/v1/reservation' => Http::response([
                'message' => 'Tento termín je už obsadený'
            ], 422),
        ]);

        [$profile, $service] = $this->createPakavozService();

        $response = $this->postJson('/api/appointments', [
            'profile_id' => $profile->id,
            'service_id' => $service->id,
            'start_at' => $targetDate . ' 08:00',
            'customer_name' => 'Ján Novák',
            'customer_phone' => '+421900111222',
            'evc' => 'BA123XY'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['start_at']);
        $this->assertEquals('Tento termín je už obsadený', $response->json('errors.start_at.0'));
    }
}
