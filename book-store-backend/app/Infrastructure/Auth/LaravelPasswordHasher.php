<?php

namespace App\Infrastructure\Auth;

use App\Application\Identity\Interfaces\PasswordHasherInterface;
use Illuminate\Contracts\Hashing\Hasher;

final readonly class LaravelPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private Hasher $hasher,
    ) {}

    public function hash(string $plain): string
    {
        return $this->hasher->make($plain);
    }

    public function verify(string $plain, string $hashed): bool
    {
        return $this->hasher->check($plain, $hashed);
    }
}
