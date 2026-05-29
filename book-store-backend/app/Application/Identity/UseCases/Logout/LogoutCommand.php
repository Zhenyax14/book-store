<?php

declare(strict_types=1);

namespace App\Application\Identity\UseCases\Logout;

use App\Domain\Identity\ValueObjects\UserId;

final readonly class LogoutCommand
{
    public function __construct(
        public UserId $userId,
    ) {}
}
