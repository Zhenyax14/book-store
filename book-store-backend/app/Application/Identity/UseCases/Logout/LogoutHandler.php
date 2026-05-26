<?php

declare(strict_types=1);

namespace App\Application\Identity\UseCases\Logout;

use App\Domain\Identity\Interfaces\AuthenticationServiceInterface;

final readonly class LogoutHandler
{
    public function __construct(
        private AuthenticationServiceInterface $auth,
    ) {}

    public function handle(LogoutCommand $command): void
    {
        $this->auth->revokeCurrentToken($command->userId);
    }
}
