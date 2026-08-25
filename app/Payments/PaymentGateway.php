<?php

namespace App\Payments;

use App\Models\Payment;

interface PaymentGateway
{
    public function isReady(): bool;

    public function start(Payment $payment): array;
}
