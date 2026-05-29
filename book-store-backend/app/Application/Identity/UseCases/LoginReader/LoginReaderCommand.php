<?php

declare(strict_types=1);

namespace App\Application\Identity\UseCases\LoginReader;

final readonly class LoginReaderCommand
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
