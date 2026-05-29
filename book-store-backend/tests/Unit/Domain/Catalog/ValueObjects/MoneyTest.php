<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Catalog\ValueObjects;

use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public const string DEFAULT_CURRENCY = 'EUR';

    public function test_amount_cannot_be_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Money(-100, new Currency(self::DEFAULT_CURRENCY));
    }

    public function test_add_same_currency(): void
    {
        $a      = Money::ofEur(10000);
        $b      = Money::ofEur(20000);
        $result = $a->add($b);

        $this->assertEquals(30000, $result->amount);
    }

    public function test_add_different_currencies_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::ofEur(10000)->add(Money::ofUsd(10000));
    }

    public function test_subtract(): void
    {
        $result = Money::ofEur(50000)->subtract(Money::ofEur(10000));

        $this->assertEquals(40000, $result->amount);
    }

    public function test_subtract_larger_amount_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::ofEur(10000)->subtract(Money::ofEur(50000));
    }

    public function test_multiply(): void
    {
        $result = Money::ofEur(10000)->multiply(3);

        $this->assertEquals(30000, $result->amount);
    }

    public function test_is_zero(): void
    {
        $this->assertTrue(Money::zero(new Currency(self::DEFAULT_CURRENCY))->isZero());
        $this->assertFalse(Money::ofEur(100)->isZero());
    }

    public function test_equals(): void
    {
        $a = Money::ofEur(10000);
        $b = Money::ofEur(10000);
        $c = Money::ofEur(20000);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    public function test_format_usd(): void
    {
        $this->assertEquals('$500.00', Money::ofUsd(50000)->format());
    }

    public function test_format_eur(): void
    {
        $this->assertEquals('500.00 €', Money::ofEur(50000)->format());
    }

    public function test_immutability(): void
    {
        $original = Money::ofEur(10000);
        $result   = $original->add(Money::ofEur(5000));

        $this->assertEquals(10000, $original->amount);
        $this->assertEquals(15000, $result->amount);
    }
}
