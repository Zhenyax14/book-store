<?php

declare(strict_types=1);

namespace App\Domain\Identity\ValueObjects;

final readonly class UserId
{
    public function __construct(public int $value) {}
}
