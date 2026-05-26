<?php

declare(strict_types=1);

namespace App\Application\Identity\Interfaces;

interface PasswordHasherInterface
{
    public function hash(string $plain): string;

    public function verify(string $plain, string $hashed): bool;
}
