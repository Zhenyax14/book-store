<?php

declare(strict_types=1);

namespace App\Application\Identity\UseCases\LoginAdmin;

use App\Application\Identity\Interfaces\PasswordHasherInterface;
use App\Application\Identity\UseCases\AuthResult;
use App\Domain\Identity\Exceptions\InvalidCredentialsException;
use App\Domain\Identity\Interfaces\AuthenticationServiceInterface;
use App\Domain\Identity\Interfaces\UserRepositoryInterface;
use App\Domain\Identity\ValueObjects\Email;
use App\Domain\Shared\Enums\RoleEnum;

final readonly class LoginAdminHandler
{
    public function __construct(
        private UserRepositoryInterface        $users,
        private AuthenticationServiceInterface $auth,
        private PasswordHasherInterface        $hasher,
    ) {}

    public function handle(LoginAdminCommand $command): AuthResult
    {
        $email  = new Email($command->email);
        $user = $this->users->findByEmail($email);

        if (null === $user
            || RoleEnum::ADMIN !== $user->getRole()
            || ! $this->hasher->verify($command->plainPassword, $user->getPassword()->value)
        ) {
            throw InvalidCredentialsException::create();
        }

        $token = $this->auth->issueToken($user);

        return new AuthResult(user: $user, token: $token);
    }
}
