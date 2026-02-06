<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);

        $owner = User::factory()->create(['role' => 'owner']);

        Profile::create([
            'owner_id' => $owner->id,
            'name' => 'Premium Shop',
            'slug' => 'premium-shop',
            'subscription_plan' => 'premium',
        ]);

        Profile::create([
            'owner_id' => $owner->id,
            'name' => 'Basic Shop',
            'slug' => 'basic-shop',
            'subscription_plan' => 'basic',
        ]);

        Profile::create([
            'owner_id' => $owner->id,
            'name' => 'Trial Shop',
            'slug' => 'trial-shop',
            'subscription_plan' => 'free',
        ]);
    }

    public function test_admin_can_see_all_profiles()
    {
        $response = $this->actingAs($this->admin)->get('/admin/profiles');

        $response->assertStatus(200);
        $response->assertSee('Premium Shop');
        $response->assertSee('Basic Shop');
        $response->assertSee('Trial Shop');
    }

    public function test_admin_can_filter_premium_profiles()
    {
        $response = $this->actingAs($this->admin)->get('/admin/profiles?plan=premium');

        $response->assertStatus(200);
        $response->assertSee('Premium Shop');
        $response->assertDontSee('Basic Shop');
        $response->assertDontSee('Trial Shop');
    }

    public function test_admin_can_filter_basic_profiles()
    {
        $response = $this->actingAs($this->admin)->get('/admin/profiles?plan=basic');

        $response->assertStatus(200);
        $response->assertDontSee('Premium Shop');
        $response->assertSee('Basic Shop');
        $response->assertDontSee('Trial Shop');
    }

    public function test_admin_can_filter_trial_profiles()
    {
        $response = $this->actingAs($this->admin)->get('/admin/profiles?plan=free');

        $response->assertStatus(200);
        $response->assertDontSee('Premium Shop');
        $response->assertDontSee('Basic Shop');
        $response->assertSee('Trial Shop');
    }
}
