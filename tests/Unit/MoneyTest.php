<?php

namespace Tests\Unit;

use App\Support\Money;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    public function test_it_formats_pen_without_changing_the_decimal_value(): void
    {
        config()->set('shop.currency', 'PEN');

        $this->assertSame('S/ 10.00', Money::format('10.00'));
        $this->assertSame('S/ 1,250.50', Money::format('1250.50'));
    }

    public function test_backend_rejects_an_unsupported_configured_currency(): void
    {
        config()->set('shop.currency', 'USD');

        $this->assertSame('PEN', Money::currencyCode());
    }

    public function test_it_calculates_only_a_real_visual_discount(): void
    {
        $this->assertSame(50, Money::discountPercentage('50.00', '100.00'));
        $this->assertNull(Money::discountPercentage('100.00', '90.00'));
        $this->assertNull(Money::discountPercentage(null, '90.00'));
    }
}
