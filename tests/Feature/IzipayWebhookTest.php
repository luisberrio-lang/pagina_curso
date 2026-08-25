<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentAttemptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class IzipayWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.izipay.hash_key' => 'clave-hash-solo-testing']);
    }

    public function test_authenticated_success_event_confirms_payment_and_order(): void
    {
        [$order, $payment] = $this->payment();
        $response = $this->send($payment, $this->payload($order, $payment));

        $response->assertOk()->assertJson(['accepted' => true, 'result' => 'paid']);
        $this->assertSame(Payment::STATUS_PAID, $payment->refresh()->status);
        $this->assertSame(Order::PAYMENT_PAID, $order->refresh()->payment_status);
        $this->assertSame(Order::STATUS_CONFIRMED, $order->status);
        $this->assertDatabaseCount('payment_events', 1);
    }

    public function test_invalid_signature_and_unknown_reference_are_rejected(): void
    {
        [$order, $payment] = $this->payment();
        $payload = $this->payload($order, $payment);
        $this->postJson(route('izipay.webhook'), [
            'transactionId' => $payment->provider_transaction_id,
            'payloadHttp' => $payload,
            'signature' => 'invalida',
        ], ['transactionId' => $payment->provider_transaction_id])->assertUnauthorized();

        $unknown = str_repeat('9', 20);
        $decoded = json_decode($payload, true);
        $decoded['transactionId'] = $unknown;
        $this->sendRaw($unknown, json_encode($decoded, JSON_UNESCAPED_UNICODE))->assertNotFound();
        $this->assertSame(Payment::STATUS_PENDING, $payment->refresh()->status);
    }

    public function test_duplicate_event_has_one_commercial_effect(): void
    {
        [$order, $payment] = $this->payment();
        $payload = $this->payload($order, $payment);
        $this->send($payment, $payload)->assertOk();
        $this->send($payment, $payload)->assertOk()->assertJson(['result' => 'duplicate']);

        $this->assertDatabaseCount('payment_events', 1);
        $this->assertSame(Payment::STATUS_PAID, $payment->refresh()->status);
    }

    public function test_amount_currency_or_order_mismatch_never_marks_paid(): void
    {
        foreach ([['amount' => '9.99'], ['amount' => 'invalido'], ['currency' => 'USD'], ['orderNumber' => 'OTRA-ORDEN']] as $change) {
            [$order, $payment] = $this->payment();
            $payload = $this->payload($order, $payment, '00', $change);
            $this->send($payment, $payload)->assertOk()->assertJson(['result' => 'requires_review']);
            $this->assertSame(Payment::STATUS_REQUIRES_REVIEW, $payment->refresh()->status);
            $this->assertSame(Order::PAYMENT_PENDING, $order->refresh()->payment_status);
        }
    }

    public function test_late_failed_event_cannot_degrade_paid_payment(): void
    {
        [$order, $payment] = $this->payment();
        $this->send($payment, $this->payload($order, $payment))->assertOk();
        $this->send($payment, $this->payload($order, $payment, 'A02'))->assertOk()->assertJson(['result' => 'failed']);

        $this->assertSame(Payment::STATUS_PAID, $payment->refresh()->status);
        $this->assertSame(Order::PAYMENT_PAID, $order->refresh()->payment_status);
        $this->assertDatabaseCount('payment_events', 2);
    }

    public function test_manipulated_outer_fields_do_not_replace_signed_payload(): void
    {
        [$order, $payment] = $this->payment();
        $payload = $this->payload($order, $payment);
        $body = $this->body($payment->provider_transaction_id, $payload);
        $body['amount'] = '0.01';
        $body['currency'] = 'USD';
        $this->postJson(route('izipay.webhook'), $body, ['transactionId' => $payment->provider_transaction_id])->assertOk();

        $this->assertSame('10.00', $payment->refresh()->base_amount);
        $this->assertSame('PEN', $payment->base_currency);
    }

    private function payment(): array
    {
        $order = Order::forceCreate([
            'order_number' => 'ORD-2026-'.strtoupper(Str::random(10)), 'public_token' => Str::random(64),
            'checkout_token_hash' => hash('sha256', (string) Str::uuid()), 'first_name' => 'Ana', 'last_name' => 'Prueba',
            'email' => 'ana@example.com', 'phone' => '999888777', 'subtotal' => '10.00', 'total' => '10.00',
            'currency' => 'PEN', 'status' => Order::STATUS_PENDING, 'payment_status' => Order::PAYMENT_PENDING,
        ]);

        return [$order, app(PaymentAttemptService::class)->create($order)];
    }

    private function payload(Order $order, Payment $payment, string $code = '00', array $changes = []): string
    {
        $providerOrder = array_merge([
            'orderNumber' => $order->order_number, 'currency' => 'PEN', 'amount' => '10.00',
            'referenceNumber' => 'REF-TEST-1', 'uniqueId' => 'UNIQUE-TEST-1',
        ], $changes);

        return json_encode([
            'code' => $code,
            'message' => $code === '00' ? 'Operación exitosa' : 'Rechazado',
            'response' => ['payMethod' => 'CARD', 'order' => [$providerOrder]],
            'transactionId' => $payment->provider_transaction_id,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function send(Payment $payment, string $payload)
    {
        return $this->sendRaw($payment->provider_transaction_id, $payload);
    }

    private function sendRaw(string $transactionId, string $payload)
    {
        return $this->postJson(route('izipay.webhook'), $this->body($transactionId, $payload), ['transactionId' => $transactionId]);
    }

    private function body(string $transactionId, string $payload): array
    {
        return [
            'transactionId' => $transactionId,
            'payloadHttp' => $payload,
            'signature' => base64_encode(hash_hmac('sha256', $payload, 'clave-hash-solo-testing', true)),
        ];
    }
}
