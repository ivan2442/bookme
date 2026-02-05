<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_see_their_invoices()
    {
        $user = User::factory()->create(['role' => 'owner']);
        $profile = Profile::create([
            'owner_id' => $user->id,
            'name' => 'Owner Shop',
            'slug' => 'owner-shop',
            'email' => 'owner@example.com',
            'category' => 'Test',
            'city' => 'Bratislava',
        ]);

        $invoice = Invoice::create([
            'profile_id' => $profile->id,
            'invoice_number' => 'INV-001',
            'amount' => 100,
            'currency' => 'EUR',
            'status' => 'unpaid',
            'due_at' => now()->addDays(14),
        ]);

        $response = $this->actingAs($user)->get(route('owner.invoices'));

        $response->assertStatus(200);
        $response->assertSee('INV-001');
        $response->assertSee('Owner Shop');
    }

    public function test_owner_cannot_see_other_invoices()
    {
        $user1 = User::factory()->create(['role' => 'owner']);
        $profile1 = Profile::create([
            'owner_id' => $user1->id,
            'name' => 'Shop 1',
            'slug' => 'shop-1',
            'email' => 'shop1@example.com',
            'category' => 'Test',
            'city' => 'Bratislava',
        ]);

        $user2 = User::factory()->create(['role' => 'owner']);
        $profile2 = Profile::create([
            'owner_id' => $user2->id,
            'name' => 'Shop 2',
            'slug' => 'shop-2',
            'email' => 'shop2@example.com',
            'category' => 'Test',
            'city' => 'Bratislava',
        ]);

        $invoice1 = Invoice::create([
            'profile_id' => $profile1->id,
            'invoice_number' => 'INV-OWNER1',
            'amount' => 100,
            'currency' => 'EUR',
            'status' => 'unpaid',
            'due_at' => now()->addDays(14),
        ]);

        $invoice2 = Invoice::create([
            'profile_id' => $profile2->id,
            'invoice_number' => 'INV-OTHER',
            'amount' => 200,
            'currency' => 'EUR',
            'status' => 'unpaid',
            'due_at' => now()->addDays(14),
        ]);

        $response = $this->actingAs($user1)->get(route('owner.invoices'));

        $response->assertStatus(200);
        $response->assertSee('INV-OWNER1');
        $response->assertDontSee('INV-OTHER');
    }

    public function test_owner_can_preview_their_invoice()
    {
        $user = User::factory()->create(['role' => 'owner']);
        $profile = Profile::create([
            'owner_id' => $user->id,
            'name' => 'Owner Shop',
            'slug' => 'owner-shop',
            'email' => 'owner@example.com',
            'category' => 'Test',
            'city' => 'Bratislava',
        ]);

        $invoice = Invoice::create([
            'profile_id' => $profile->id,
            'invoice_number' => 'INV-001',
            'amount' => 100,
            'currency' => 'EUR',
            'status' => 'unpaid',
            'due_at' => now()->addDays(14),
        ]);

        $response = $this->actingAs($user)->get(route('owner.invoices.preview', $invoice));

        $response->assertStatus(200);
        $response->assertSee('INV-001');
    }

    public function test_owner_cannot_preview_other_invoice()
    {
        $user1 = User::factory()->create(['role' => 'owner']);
        $user2 = User::factory()->create(['role' => 'owner']);

        $profile2 = Profile::create([
            'owner_id' => $user2->id,
            'name' => 'Shop 2',
            'slug' => 'shop-2',
            'email' => 'shop2@example.com',
            'category' => 'Test',
            'city' => 'Bratislava',
        ]);

        $invoice2 = Invoice::create([
            'profile_id' => $profile2->id,
            'invoice_number' => 'INV-OTHER',
            'amount' => 200,
            'currency' => 'EUR',
            'status' => 'unpaid',
            'due_at' => now()->addDays(14),
        ]);

        $response = $this->actingAs($user1)->get(route('owner.invoices.preview', $invoice2));

        $response->assertStatus(403);
    }
}
