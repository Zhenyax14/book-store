<?php

declare(strict_types=1);

namespace App\Domain\Identity\Events;

final readonly class UserRegistered
{
    public function __construct(
        public int    $userId,
        public string $userName,
        public string $userEmail,
    ) {}
}
