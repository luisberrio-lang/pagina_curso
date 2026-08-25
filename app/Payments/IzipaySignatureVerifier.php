<?php

namespace App\Payments;

class IzipaySignatureVerifier
{
    public function verify(string $payload, string $signature): bool
    {
        $hashKey = (string) config('services.izipay.hash_key');
        if ($hashKey === '' || $payload === '' || $signature === '') {
            return false;
        }

        $expected = base64_encode(hash_hmac('sha256', $payload, $hashKey, true));

        return hash_equals($expected, $signature);
    }
}
