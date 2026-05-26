<?php

declare(strict_types=1);

namespace App\Domain\Access\Interfaces;

use App\Domain\Access\Entities\UserBookAccess;

interface UserBookAccessRepositoryInterface
{
    public function findByUserAndBook(int $userId, int $bookId): ?UserBookAccess;

    public function save(UserBookAccess $access): UserBookAccess;

    public function hasAccess(int $userId, int $bookId): bool;
}
