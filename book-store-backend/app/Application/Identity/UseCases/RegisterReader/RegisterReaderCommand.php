<?php

declare(strict_types=1);

namespace App\Application\Identity\UseCases\RegisterReader;

final readonly class RegisterReaderCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $plainPassword,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            plainPassword: $data['password'],
        );
    }
}
