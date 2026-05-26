<?php

declare(strict_types=1);

namespace App\Domain\Identity\ValueObjects;

final readonly class AuthToken
{
    public function __construct(public string $value) {}
}
