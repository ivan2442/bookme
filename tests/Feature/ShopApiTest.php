<?php

namespace Tests\Feature;

use App\Models\CalendarSetting;
use App\Models\Profile;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_shops_index_includes_is_pakavoz_enabled_for_services()
    {
        $user = User::factory()->create();
        $profile = Profile::create([
            'owner_id' => $user->id,
            'name' => 'Test Shop',
            'slug' => 'test-shop',
            'city' => 'Bratislava',
            'status' => 'published',
        ]);

        CalendarSetting::create([
            'profile_id' => $profile->id,
            'is_public' => true,
        ]);

        Service::create([
            'profile_id' => $profile->id,
            'name' => 'Pakavoz Service',
            'base_price' => 10,
            'base_duration_minutes' => 30,
            'is_pakavoz_enabled' => true,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/shops');

        $response->assertStatus(200);

        // Kontrola, či prvá služba v prvom obchode obsahuje is_pakavoz_enabled
        $response->assertJsonPath('data.0.services.0.is_pakavoz_enabled', true);
    }
}
