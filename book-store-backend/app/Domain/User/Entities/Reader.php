<?php

declare(strict_types=1);

namespace App\Domain\User\Entities;

final readonly class Reader
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public bool $hasActiveSubscription = false,
        public bool $hasBooks = false,
        public string $created_at,
    ) {}
}
