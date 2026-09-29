<?php

namespace App\Payments;

use App\Models\Payment;
use LogicException;

class IzipayService implements PaymentGateway
{
    public function isReady(): bool
    {
        $configuration = (array) config('services.izipay', []);

        return ($configuration['payments_enabled'] ?? false) === true
            && in_array($configuration['environment'] ?? null, ['sandbox', 'production'], true)
            && filled($configuration['merchant_code'] ?? null)
            && filled($configuration['api_key'] ?? null)
            && filled($configuration['hash_key'] ?? null)
            && filled($configuration['public_key'] ?? null);
    }

    public function start(Payment $payment): array
    {
        throw new LogicException('La generación oficial del token de sesión Izipay aún no está configurada.');
    }
}
