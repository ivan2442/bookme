<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Services\RevolutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RevolutIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_save_revolut_settings()
    {
        $response = $this->actingAs($this->admin)->post('/admin/billing-settings', [
            'revolut_client_id' => 'test-client-id',
            'revolut_jwt' => 'test-jwt',
            'revolut_refresh_token' => 'test-refresh-token',
        ]);

        $response->assertStatus(302);
        $this->assertEquals('test-client-id', Setting::get('revolut_client_id'));
        $this->assertEquals('test-jwt', Setting::get('revolut_jwt'));
        $this->assertEquals('test-refresh-token', Setting::get('revolut_refresh_token'));
    }

    public function test_payments_page_shows_revolut_transactions_when_configured()
    {
        Setting::set('revolut_client_id', 'client-id');
        Setting::set('revolut_jwt', 'jwt');
        Setting::set('revolut_refresh_token', 'refresh-token');

        Http::fake([
            'https://b2b.revolut.com/api/1.0/auth/token' => Http::response([
                'access_token' => 'fake-access-token'
            ], 200),
            'https://b2b.revolut.com/api/1.0/transactions*' => Http::response([
                [
                    'id' => 'tx_123',
                    'reference' => 'Payment for STK',
                    'state' => 'completed',
                    'type' => 'ATM',
                    'created_at' => '2024-05-20T10:00:00Z',
                    'legs' => [
                        [
                            'amount' => 20.00,
                            'currency' => 'EUR'
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/payments');

        $response->assertStatus(200);
        $response->assertSee('Payment for STK');
        $response->assertSee('20.00 EUR');
        $response->assertSee('tx_123');
    }

    public function test_payments_page_shows_configuration_warning_when_not_configured()
    {
        // Ensure not configured
        Setting::where('key', 'like', 'revolut_%')->delete();

        $response = $this->actingAs($this->admin)->get('/admin/payments');

        $response->assertStatus(200);
        $response->assertSee('Revolut API nie je nakonfigurované');
    }
}
