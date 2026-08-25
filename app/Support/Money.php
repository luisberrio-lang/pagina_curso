<?php

namespace App\Support;

use InvalidArgumentException;

final class Money
{
    private const SYMBOLS = ['PEN' => 'S/'];

    public static function format(string|int|null $amount, ?string $currency = null): ?string
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        $currency = self::currencyCode($currency);
        [$integer, $decimal] = self::parts((string) $amount);
        $integer = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $integer);

        return self::symbol($currency).' '.$integer.'.'.$decimal;
    }

    public static function symbol(?string $currency = null): string
    {
        $currency = self::currencyCode($currency);

        return self::SYMBOLS[$currency] ?? $currency;
    }

    public static function discountPercentage(string|int|null $current, string|int|null $previous): ?int
    {
        if ($current === null || $previous === null || $current === '' || $previous === '') {
            return null;
        }

        $currentMinor = self::toMinorUnits((string) $current);
        $previousMinor = self::toMinorUnits((string) $previous);

        if ($currentMinor <= 0 || $previousMinor <= $currentMinor) {
            return null;
        }

        return (int) round((($previousMinor - $currentMinor) * 100) / $previousMinor);
    }

    public static function currencyCode(?string $currency = null): string
    {
        $currency = strtoupper($currency ?: (string) config('shop.currency', 'PEN'));
        $supported = config('shop.supported_currencies', ['PEN']);

        return in_array($currency, $supported, true) ? $currency : 'PEN';
    }

    public static function isPositive(string|int|null $amount): bool
    {
        return $amount !== null && $amount !== '' && self::toMinorUnits((string) $amount) > 0;
    }

    public static function toMinorUnits(string|int $amount): int
    {
        [$integer, $decimal] = self::parts((string) $amount);

        return ((int) $integer * 100) + (int) $decimal;
    }

    public static function fromMinorUnits(int $amount): string
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('El monto no puede ser negativo.');
        }

        return intdiv($amount, 100).'.'.str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }

    private static function parts(string $amount): array
    {
        $amount = trim($amount);
        if (! preg_match('/^\d+(?:\.\d+)?$/', $amount)) {
            throw new InvalidArgumentException('El monto debe ser un decimal positivo válido.');
        }

        [$integer, $decimal] = array_pad(explode('.', $amount, 2), 2, '');

        return [ltrim($integer, '0') ?: '0', str_pad(substr($decimal, 0, 2), 2, '0')];
    }

}
