<?php

declare(strict_types=1);

namespace App\Domain\Identity\Interfaces;

use App\Domain\Identity\Entities\User;
use App\Domain\Identity\ValueObjects\Email;

interface UserRepositoryInterface
{
    public function save(User $user): User;

    public function findByEmail(Email $email): ?User;

    public function existsByEmail(Email $email): bool;
}
