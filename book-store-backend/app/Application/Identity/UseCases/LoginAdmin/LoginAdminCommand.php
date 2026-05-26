<?php

declare(strict_types=1);

namespace App\Application\Identity\UseCases\LoginAdmin;

final readonly class LoginAdminCommand
{
    public function __construct(
        public string $email,
        public string $plainPassword,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            plainPassword: $data['password'],
        );
    }
}
