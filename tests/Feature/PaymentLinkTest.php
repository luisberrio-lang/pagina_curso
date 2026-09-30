<?php

namespace Tests\Feature;

use App\Models\PaymentLink;
use App\Models\User;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_list_and_open_payment_link_creation(): void
    {
        $this->get(route('admin.payment-links.index'))->assertRedirect(route('login'));
        $this->get(route('admin.payment-links.create'))->assertRedirect(route('login'));

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get(route('admin.payment-links.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.payment-links.create'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.payment-links.store'), $this->validPayload())->assertForbidden();

        $admin = $this->admin();
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('admin.payment-links.index').'"', false)
            ->assertSee('Enlaces de pago');
        $this->actingAs($admin)
            ->get(route('admin.payment-links.index'))
            ->assertOk()
            ->assertSee('Enlaces de pago')
            ->assertSee(route('admin.payment-links.create'));
    }

    public function test_admin_can_create_a_server_owned_pen_payment_link(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.payment-links.store'), $this->validPayload())
            ->assertRedirect(route('admin.payment-links.index'));

        $link = PaymentLink::query()->firstOrFail();
        $this->assertSame('125.50', $link->amount);
        $this->assertSame('PEN', $link->currency);
        $this->assertSame($admin->id, $link->created_by);
        $this->assertMatchesRegularExpression('/^PL-\d{4}-[A-Z0-9]{8}$/', $link->reference);
        $this->assertSame(hash('sha256', $link->publicToken()), $link->token_hash);
        $this->assertNotSame($link->publicToken(), $link->token_ciphertext);
    }

    public function test_invalid_amount_currency_and_expiration_are_rejected(): void
    {
        $admin = $this->admin();

        foreach ([
            ['amount' => '0'],
            ['amount' => '-1'],
            ['amount' => '1.999'],
            ['amount' => '100000.01'],
            ['currency' => 'USD'],
            ['expires_at' => now()->subMinute()->format('Y-m-d H:i:s')],
        ] as $invalid) {
            $this->actingAs($admin)
                ->post(route('admin.payment-links.store'), array_merge($this->validPayload(), $invalid))
                ->assertSessionHasErrors(array_key_first($invalid));
        }

        $this->assertDatabaseCount('payment_links', 0);
    }

    public function test_tokens_and_references_are_unique_and_non_enumerable(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.payment-links.store'), $this->validPayload());
        $this->actingAs($admin)->post(route('admin.payment-links.store'), $this->validPayload(['customer_email' => 'dos@example.com']));

        $links = PaymentLink::query()->get();
        $this->assertCount(2, $links);
        $this->assertNotSame($links[0]->token_hash, $links[1]->token_hash);
        $this->assertNotSame($links[0]->reference, $links[1]->reference);

        foreach (['1', '2', 'test', Str::random(64)] as $token) {
            $this->get('/pago/'.$token)->assertNotFound();
        }
    }

    public function test_public_page_uses_database_amount_and_hides_internal_data(): void
    {
        $link = $this->paymentLink([
            'customer_name' => '<script>alert(1)</script>',
            'concept' => '<img src=x onerror=alert(1)>',
            'internal_note' => 'NOTA-INTERNA-SECRETA',
            'amount' => '125.50',
        ]);

        $response = $this->get($link->publicUrl().'?amount=1')
            ->assertOk()
            ->assertSee('S/ 125.50')
            ->assertSee('El monto fue definido previamente y no puede modificarse.')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('NOTA-INTERNA-SECRETA')
            ->assertDontSee($link->reference);

        $this->assertSame('125.50', $link->fresh()->amount);
        $this->post($link->publicUrl(), ['amount' => '0.10'])->assertStatus(405);
        $this->assertSame('125.50', $link->fresh()->amount);
        $this->assertStringNotContainsString('name="amount"', $response->getContent());
    }

    public function test_expired_link_is_interpreted_as_expired_without_deletion_or_payment_attempt(): void
    {
        $link = $this->paymentLink(['expires_at' => now()->subMinute()]);

        $this->get($link->publicUrl())
            ->assertOk()
            ->assertSee('Enlace expirado')
            ->assertDontSee('Iniciar pago');

        $this->assertDatabaseHas('payment_links', ['id' => $link->id, 'status' => PaymentLink::STATUS_EXPIRED]);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_admin_can_cancel_pending_link_but_not_paid_link(): void
    {
        $admin = $this->admin();
        $pending = $this->paymentLink();

        $this->actingAs($admin)
            ->patch(route('admin.payment-links.cancel', $pending))
            ->assertRedirect(route('admin.payment-links.index'));

        $this->assertSame(PaymentLink::STATUS_CANCELLED, $pending->fresh()->status);
        $this->assertNotNull($pending->fresh()->cancelled_at);

        $paid = $this->paymentLink(['status' => PaymentLink::STATUS_PAID, 'paid_at' => now()]);
        $this->actingAs($admin)
            ->patch(route('admin.payment-links.cancel', $paid))
            ->assertSessionHasErrors('payment_link');

        $this->assertSame(PaymentLink::STATUS_PAID, $paid->fresh()->status);
        $this->assertNull($paid->fresh()->cancelled_at);
    }

    public function test_admin_list_exposes_safe_copy_open_and_whatsapp_actions(): void
    {
        $admin = $this->admin();
        $link = $this->paymentLink(['customer_phone' => '+51 999 222 333']);

        $response = $this->actingAs($admin)
            ->get(route('admin.payment-links.index'))
            ->assertOk()
            ->assertSee('Copiar enlace')
            ->assertSee('Abrir enlace')
            ->assertSee('Enviar por WhatsApp')
            ->assertSee($link->publicUrl())
            ->assertSee('https://wa.me/51999222333', false)
            ->assertDontSee($link->token_hash);

        $this->assertStringContainsString(rawurlencode($link->publicUrl()), $response->getContent());
    }

    public function test_disabled_izipay_creates_no_payment_and_dcc_remains_inactive(): void
    {
        config()->set('services.izipay.payments_enabled', false);
        $link = $this->paymentLink();

        $this->get($link->publicUrl())
            ->assertOk()
            ->assertSee('Pago en línea temporalmente no disponible.')
            ->assertDontSee('Iniciar pago');

        $this->assertFalse(app(PaymentGateway::class)->isReady());
        $this->assertFalse((bool) config('services.izipay.dcc_active', false));
        $this->assertDatabaseCount('payments', 0);
        $this->assertSame(PaymentLink::STATUS_PENDING, $link->fresh()->status);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Cliente Internacional',
            'customer_email' => 'cliente@example.com',
            'customer_phone' => '+51 999 111 222',
            'concept' => 'Asesoría personalizada',
            'amount' => '125.50',
            'currency' => 'PEN',
            'expires_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'internal_note' => 'Acuerdo por WhatsApp',
        ], $overrides);
    }

    private function paymentLink(array $overrides = []): PaymentLink
    {
        $token = Str::random(64);
        $link = new PaymentLink;
        $link->forceFill(array_merge([
            'reference' => 'PL-'.now()->format('Y').'-'.strtoupper(Str::random(8)),
            'token_hash' => hash('sha256', $token),
            'token_ciphertext' => Crypt::encryptString($token),
            'customer_name' => 'Cliente de prueba',
            'customer_email' => 'cliente@example.com',
            'customer_phone' => null,
            'concept' => 'Servicio personalizado',
            'amount' => '125.50',
            'currency' => 'PEN',
            'status' => PaymentLink::STATUS_PENDING,
            'expires_at' => null,
            'paid_at' => null,
            'cancelled_at' => null,
            'internal_note' => 'Dato privado',
            'created_by' => $this->admin()->id,
        ], $overrides))->save();

        return $link;
    }
}
