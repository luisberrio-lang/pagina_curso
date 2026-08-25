<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\PaymentAttemptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_list_and_view_payments(): void
    {
        $payment = app(PaymentAttemptService::class)->create($this->order());
        $this->get(route('admin.payments.index'))->assertRedirect(route('login'));
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get(route('admin.payments.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.payments.show', $payment))->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.payments.index'))->assertOk()->assertSee($payment->payment_number);
        $this->actingAs($admin)->get(route('admin.payments.show', $payment))->assertOk()->assertSee($payment->provider_transaction_id);
    }

    private function order(): Order
    {
        return Order::forceCreate([
            'order_number' => 'ORD-2026-'.strtoupper(Str::random(10)), 'public_token' => Str::random(64),
            'checkout_token_hash' => hash('sha256', (string) Str::uuid()), 'first_name' => 'Ana', 'last_name' => 'Prueba',
            'email' => 'ana@example.com', 'phone' => '999888777', 'subtotal' => '10.00', 'total' => '10.00',
            'currency' => 'PEN', 'status' => Order::STATUS_PENDING, 'payment_status' => Order::PAYMENT_PENDING,
        ]);
    }
}
