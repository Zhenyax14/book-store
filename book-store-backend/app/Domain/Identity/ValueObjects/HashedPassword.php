<?php

declare(strict_types=1);

namespace App\Domain\Identity\ValueObjects;

final readonly class HashedPassword
{
    public function __construct(public readonly string $value) {}

    public static function fromHashed(string $hashed): self
    {
        return new self($hashed);
    }
}
