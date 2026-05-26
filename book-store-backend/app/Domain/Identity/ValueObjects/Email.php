<?php

declare(strict_types=1);

namespace App\Domain\Identity\ValueObjects;

final readonly class Email
{
    public readonly string $value;

    public function __construct(string $value)
    {
        $normalized = mb_strtolower(mb_trim($value));
        if ( ! filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email: {$value}");
        }
        $this->value = $normalized;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
