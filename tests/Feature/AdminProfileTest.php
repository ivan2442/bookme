<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_profile()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'owner']);

        $logo = UploadedFile::fake()->image('logo.png');
        $banner = UploadedFile::fake()->image('banner.png');

        $profile = Profile::create([
            'owner_id' => $owner->id,
            'name' => 'Testovacia prevádzka',
            'slug' => 'testovacia-prevadzka',
            'city' => 'Bratislava',
            'logo_path' => $logo->store('profiles/logos', 'public'),
            'banner_path' => $banner->store('profiles/banners', 'public'),
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('profiles', ['id' => $profile->id]);
        Storage::disk('public')->assertExists($profile->logo_path);
        Storage::disk('public')->assertExists($profile->banner_path);

        $response = $this->actingAs($admin)
            ->delete("/admin/profiles/{$profile->id}");

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Prevádzka bola odstránená.');

        $this->assertDatabaseMissing('profiles', ['id' => $profile->id]);
        Storage::disk('public')->assertMissing($profile->logo_path);
        Storage::disk('public')->assertMissing($profile->banner_path);
    }

    public function test_non_admin_cannot_delete_profile()
    {
        $user = User::factory()->create(['role' => 'owner']);
        $profile = Profile::create([
            'owner_id' => $user->id,
            'name' => 'Testovacia prevádzka',
            'slug' => 'testovacia-prevadzka',
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->delete("/admin/profiles/{$profile->id}");

        $response->assertRedirect(route('auth.login'));
        $this->assertDatabaseHas('profiles', ['id' => $profile->id]);
    }
}
