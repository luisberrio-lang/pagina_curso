<?php

namespace Tests\Feature;

use App\Models\User;
use App\Payments\IzipayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IzipayConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_gateway_readiness_is_fail_closed(): void
    {
        $gateway = app(IzipayService::class);

        config()->set('services.izipay', $this->configuration(false));
        $this->assertFalse($gateway->isReady());

        config()->set('services.izipay', $this->configuration(true, ['api_key' => '']));
        $this->assertFalse($gateway->isReady());

        config()->set('services.izipay', $this->configuration(true, ['environment' => 'invalid']));
        $this->assertFalse($gateway->isReady());

        config()->set('services.izipay', $this->configuration(true));
        $this->assertTrue($gateway->isReady());
    }

    public function test_admin_panel_shows_only_configuration_statuses(): void
    {
        $secrets = [
            'merchant_code' => 'merchant-secret-value',
            'api_key' => 'api-secret-value',
            'hash_key' => 'hash-secret-value',
            'public_key' => 'public-secret-value',
        ];
        config()->set('services.izipay', $this->configuration(false, $secrets));
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('admin.configuration.show'));

        $response->assertOk()
            ->assertSee('Estado de Izipay')
            ->assertSee('Configurado')
            ->assertSee('Configurada');
        foreach ($secrets as $secret) {
            $response->assertDontSee($secret);
        }
    }

    private function configuration(bool $enabled, array $overrides = []): array
    {
        return array_merge([
            'payments_enabled' => $enabled,
            'environment' => 'sandbox',
            'merchant_code' => 'merchant-value',
            'api_key' => 'api-value',
            'hash_key' => 'hash-value',
            'public_key' => 'public-value',
            'sdk_url' => 'https://sandbox.invalid/sdk.js',
        ], $overrides);
    }
}
