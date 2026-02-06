<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInvoiceFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $owner;
    protected $profile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->profile = Profile::create([
            'owner_id' => $this->owner->id,
            'name' => 'Test Profile',
            'slug' => 'test-profile',
        ]);
    }

    public function test_admin_can_filter_all_invoices()
    {
        Invoice::create([
            'profile_id' => $this->profile->id,
            'invoice_number' => 'INV-001',
            'amount' => 10,
            'status' => 'paid',
            'due_at' => now()->addDays(14),
        ]);

        Invoice::create([
            'profile_id' => $this->profile->id,
            'invoice_number' => 'INV-002',
            'amount' => 20,
            'status' => 'unpaid',
            'due_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/invoices');

        $response->assertStatus(200);
        $response->assertSee('INV-001');
        $response->assertSee('INV-002');
    }

    public function test_admin_can_filter_paid_invoices()
    {
        Invoice::create([
            'profile_id' => $this->profile->id,
            'invoice_number' => 'INV-PAID',
            'amount' => 10,
            'status' => 'paid',
            'due_at' => now()->addDays(14),
        ]);

        Invoice::create([
            'profile_id' => $this->profile->id,
            'invoice_number' => 'INV-UNPAID',
            'amount' => 20,
            'status' => 'unpaid',
            'due_at' => now()->addDays(14),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/invoices?status=paid');

        $response->assertStatus(200);
        $response->assertSee('INV-PAID');
        $response->assertDontSee('INV-UNPAID');
    }

    public function test_admin_can_filter_overdue_invoices()
    {
        Invoice::create([
            'profile_id' => $this->profile->id,
            'invoice_number' => 'INV-OVERDUE',
            'amount' => 10,
            'status' => 'unpaid',
            'due_at' => now()->subDays(5),
        ]);

        Invoice::create([
            'profile_id' => $this->profile->id,
            'invoice_number' => 'INV-NOT-OVERDUE',
            'amount' => 20,
            'status' => 'unpaid',
            'due_at' => now()->addDays(5),
        ]);

        Invoice::create([
            'profile_id' => $this->profile->id,
            'invoice_number' => 'INV-PAID-OLD',
            'amount' => 30,
            'status' => 'paid',
            'due_at' => now()->subDays(10),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/invoices?status=overdue');

        $response->assertStatus(200);
        $response->assertSee('INV-OVERDUE');
        $response->assertDontSee('INV-NOT-OVERDUE');
        $response->assertDontSee('INV-PAID-OLD');
    }
}
