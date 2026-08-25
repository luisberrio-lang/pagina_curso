<?php

namespace App\Payments;

use App\Models\Payment;
use LogicException;

class IzipayService implements PaymentGateway
{
    public function isReady(): bool
    {
        return false;
    }

    public function start(Payment $payment): array
    {
        throw new LogicException('La generación oficial del token de sesión Izipay aún no está configurada.');
    }
}
