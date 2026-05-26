<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

final readonly class Currency
{
    public const array SUPPORTED = ['USD', 'EUR'];

    public function __construct(
        public string $code,
    ) {
        if ( ! in_array($code, self::SUPPORTED, true)) {
            throw new \InvalidArgumentException(
                "Unsupported currency: {$code}. Supported currencies: " . implode(', ', self::SUPPORTED),
            );
        }
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }
}
