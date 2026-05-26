<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Application\Identity\Interfaces\PasswordHasherInterface;

final class FakePasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plain): string
    {
        return 'hashed:' . $plain;
    }

    public function verify(string $plain, string $hashed): bool
    {
        return $hashed === 'hashed:' . $plain;
    }
}
