<?php

declare(strict_types=1);

namespace App\Domain\Identity\Entities;

use App\Domain\Identity\ValueObjects\Email;
use App\Domain\Identity\ValueObjects\HashedPassword;
use App\Domain\Identity\ValueObjects\UserId;
use App\Domain\Shared\Enums\RoleEnum;

final readonly class User
{
    public function __construct(
        private ?UserId        $id,
        private string         $name,
        private Email          $email,
        private HashedPassword $password,
        private RoleEnum       $role,
    ) {}

    public static function register(string $name, Email $email, HashedPassword $password): self
    {
        return new self(id: null, name: $name, email: $email, password: $password, role: RoleEnum::READER);
    }

    public function getId(): ?UserId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): HashedPassword
    {
        return $this->password;
    }

    public function getRole(): RoleEnum
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return RoleEnum::ADMIN === $this->role;
    }
}
