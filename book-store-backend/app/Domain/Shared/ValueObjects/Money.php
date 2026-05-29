<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

final readonly class Money
{
    private const string USD = 'USD';

    private const string EUR = 'EUR';

    public function __construct(
        public int      $amount,
        public Currency $currency,
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }
    }

    public static function zero(Currency $currency): self
    {
        return new self(0, $currency);
    }

    public static function ofUsd(int $cents): self
    {
        return new self($cents, new Currency(self::USD));
    }

    public static function ofEur(int $cents): self
    {
        return new self($cents, new Currency(self::EUR));
    }

    public static function fromArray(array $data): self
    {
        return new self(
            amount: $data['amount'],
            currency: new Currency($data['currency']),
        );
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        if ($other->amount > $this->amount) {
            throw new \InvalidArgumentException('Subtraction result cannot be negative');
        }

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(int $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency->equals($other->currency);
    }

    public function isGreaterThan(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount > $other->amount;
    }

    public function isZero(): bool
    {
        return 0 === $this->amount;
    }

    public function format(): string
    {
        return match ($this->currency->code) {
            self::USD => '$' . number_format($this->amount / 100, 2),
            self::EUR => number_format($this->amount / 100, 2) . ' €',
        };
    }

    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    public function toArray(): array
    {
        return [
            'amount'    => $this->amount,
            'currency'  => $this->currency->code,
            'formatted' => $this->format(),
        ];
    }

    private function assertSameCurrency(self $other): void
    {
        if ( ! $this->currency->equals($other->currency)) {
            throw new \InvalidArgumentException(
                "Cannot mix currencies: {$this->currency} and {$other->currency}",
            );
        }
    }
}
