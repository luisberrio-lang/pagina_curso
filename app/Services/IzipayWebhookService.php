<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\IzipaySignatureVerifier;
use App\Payments\WebhookRejected;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use JsonException;

class IzipayWebhookService
{
    public function __construct(private readonly IzipaySignatureVerifier $signatures) {}

    public function process(array $requestBody, ?string $headerTransactionId): string
    {
        $payload = $requestBody['payloadHttp'] ?? null;
        $signature = $requestBody['signature'] ?? null;
        $transactionId = $requestBody['transactionId'] ?? null;

        if (! is_string($payload) || ! is_string($signature) || ! is_string($transactionId)
            || $headerTransactionId === null || ! hash_equals($transactionId, $headerTransactionId)) {
            throw new WebhookRejected('Notificación incompleta.', 422);
        }

        if (! $this->signatures->verify($payload, $signature)) {
            throw new WebhookRejected('Firma inválida.', 401);
        }

        try {
            $decoded = json_decode($payload, true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new WebhookRejected('Payload inválido.', 422);
        }

        if (! is_array($decoded) || ($decoded['transactionId'] ?? null) !== $transactionId) {
            throw new WebhookRejected('Identificador de transacción inconsistente.', 422);
        }

        return DB::transaction(function () use ($decoded, $payload, $transactionId) {
            $payment = Payment::query()
                ->where('provider', 'izipay')
                ->where('provider_transaction_id', $transactionId)
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                throw new WebhookRejected('Pago no encontrado.', 404);
            }

            $eventKey = hash('sha256', 'izipay|'.$transactionId.'|'.$payload);
            $order = Order::query()->lockForUpdate()->findOrFail($payment->order_id);
            $providerOrder = data_get($decoded, 'response.order.0');
            $code = is_string($decoded['code'] ?? null) ? $decoded['code'] : null;
            $isConsistent = is_array($providerOrder)
                && ($providerOrder['orderNumber'] ?? null) === $order->order_number
                && ($providerOrder['currency'] ?? null) === $payment->base_currency
                && is_string($providerOrder['amount'] ?? null)
                && $this->amountsEqual($providerOrder['amount'], $payment->base_amount);

            $now = now();
            $inserted = DB::table('payment_events')->insertOrIgnore([
                'payment_id' => $payment->getKey(),
                'provider' => 'izipay',
                'event_key' => $eventKey,
                'payload_hash' => hash('sha256', $payload),
                'provider_code' => $code,
                'processing_status' => $isConsistent ? 'processed' : 'requires_review',
                'processed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($inserted === 0) {
                return 'duplicate';
            }

            if (! $isConsistent) {
                if ($payment->status !== Payment::STATUS_PAID) {
                    $payment->forceFill(['status' => Payment::STATUS_REQUIRES_REVIEW, 'provider_code' => $code])->save();
                }

                return 'requires_review';
            }

            $payment->forceFill([
                'provider_code' => $code,
                'provider_reference' => data_get($providerOrder, 'referenceNumber'),
            ]);

            if ($code === '00') {
                if ($payment->status !== Payment::STATUS_PAID) {
                    $payment->forceFill(['status' => Payment::STATUS_PAID, 'paid_at' => now(), 'failed_at' => null])->save();
                    $order->forceFill(['payment_status' => Order::PAYMENT_PAID, 'status' => Order::STATUS_CONFIRMED])->save();
                }

                return 'paid';
            }

            if ($payment->status !== Payment::STATUS_PAID) {
                $payment->forceFill(['status' => Payment::STATUS_FAILED, 'failed_at' => now()])->save();
            }

            return 'failed';
        }, 3);
    }

    private function amountsEqual(string $providerAmount, string $baseAmount): bool
    {
        try {
            return Money::toMinorUnits($providerAmount) === Money::toMinorUnits($baseAmount);
        } catch (\InvalidArgumentException) {
            return false;
        }
    }
}
