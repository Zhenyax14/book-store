<?php

declare(strict_types=1);

namespace App\Application\Identity\UseCases;

use App\Domain\Identity\Entities\User;
use App\Domain\Identity\ValueObjects\AuthToken;

final readonly class AuthResult
{
    public function __construct(
        public User      $user,
        public AuthToken $token,
    ) {}
}
