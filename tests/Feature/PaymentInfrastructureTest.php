<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\IzipayService;
use App\Services\PaymentAttemptService;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use LogicException;
use RuntimeException;
use Tests\TestCase;

class PaymentInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_payment_uses_order_amount_and_currency(): void
    {
        $order = $this->order();
        $payment = app(PaymentAttemptService::class)->create($order);

        $this->assertSame($order->id, $payment->order_id);
        $this->assertSame('10.00', $payment->base_amount);
        $this->assertSame('PEN', $payment->base_currency);
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertSame(1, $payment->attempt);
    }

    public function test_failed_attempt_can_be_retried_without_overwriting_history(): void
    {
        $order = $this->order();
        $first = app(PaymentAttemptService::class)->create($order);
        $first->forceFill(['status' => Payment::STATUS_FAILED, 'failed_at' => now()])->save();
        $second = app(PaymentAttemptService::class)->create($order);

        $this->assertSame(2, $second->attempt);
        $this->assertNotSame($first->payment_number, $second->payment_number);
        $this->assertDatabaseCount('payments', 2);
    }

    public function test_pending_attempt_prevents_parallel_second_attempt(): void
    {
        $order = $this->order();
        app(PaymentAttemptService::class)->create($order);

        $this->expectException(RuntimeException::class);
        app(PaymentAttemptService::class)->create($order);
    }

    public function test_paid_order_cannot_create_another_attempt(): void
    {
        $order = $this->order();
        $payment = app(PaymentAttemptService::class)->create($order);
        $payment->forceFill(['status' => Payment::STATUS_PAID, 'paid_at' => now()])->save();

        $this->expectException(RuntimeException::class);
        app(PaymentAttemptService::class)->create($order);
    }

    public function test_invalid_order_amount_or_currency_is_rejected(): void
    {
        $order = $this->order(['total' => '0.00']);
        $this->expectException(RuntimeException::class);
        app(PaymentAttemptService::class)->create($order);
    }

    public function test_payment_rejects_mass_assignment(): void
    {
        $this->expectException(MassAssignmentException::class);
        Payment::create(['order_id' => 999, 'base_amount' => '0.01', 'base_currency' => 'USD', 'status' => 'paid']);
    }

    public function test_real_gateway_start_is_disabled_without_official_token_contract(): void
    {
        $this->assertFalse(app(IzipayService::class)->isReady());
        $this->expectException(LogicException::class);
        app(IzipayService::class)->start(new Payment);
    }

    private function order(array $attributes = []): Order
    {
        return Order::forceCreate(array_merge([
            'order_number' => 'ORD-2026-'.strtoupper(Str::random(10)),
            'public_token' => Str::random(64),
            'checkout_token_hash' => hash('sha256', (string) Str::uuid()),
            'first_name' => 'Ana', 'last_name' => 'Prueba', 'email' => 'ana@example.com', 'phone' => '999888777',
            'subtotal' => '10.00', 'total' => '10.00', 'currency' => 'PEN',
            'status' => Order::STATUS_PENDING, 'payment_status' => Order::PAYMENT_PENDING,
        ], $attributes));
    }
}
