<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentAttemptService
{
    public function create(Order $order): Payment
    {
        return DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->getKey());
            if ($lockedOrder->payment_status === Order::PAYMENT_PAID
                || $lockedOrder->payments()->where('status', Payment::STATUS_PAID)->exists()) {
                throw new RuntimeException('La orden ya cuenta con un pago confirmado.');
            }

            if ($lockedOrder->payments()->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_PROCESSING])->exists()) {
                throw new RuntimeException('La orden ya tiene un intento de pago en curso.');
            }

            if (! Money::isPositive($lockedOrder->total) || $lockedOrder->currency !== Money::currencyCode()) {
                throw new RuntimeException('La orden no tiene un monto o moneda válidos para pago.');
            }

            $attempt = ((int) $lockedOrder->payments()->max('attempt')) + 1;
            $payment = new Payment;
            $payment->forceFill([
                'order_id' => $lockedOrder->getKey(),
                'provider' => 'izipay',
                'payment_number' => 'PAY-'.now()->format('Y').'-'.strtoupper(Str::random(10)),
                'public_token' => Str::random(64),
                'provider_transaction_id' => now()->format('YmdHisv').random_int(100, 999),
                'attempt' => $attempt,
                'base_amount' => $lockedOrder->total,
                'base_currency' => $lockedOrder->currency,
                'status' => Payment::STATUS_PENDING,
                'started_at' => now(),
            ])->save();

            return $payment->load('order');
        });
    }
}
