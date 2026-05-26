<?php

declare(strict_types=1);

namespace App\Domain\Identity\Interfaces;

use App\Domain\Identity\Entities\User;
use App\Domain\Identity\ValueObjects\AuthToken;
use App\Domain\Identity\ValueObjects\UserId;

interface AuthenticationServiceInterface
{
    public function issueToken(User $user): AuthToken;

    public function revokeCurrentToken(UserId $userId): void;
}
