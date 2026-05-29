<?php

declare(strict_types=1);

namespace App\Domain\Reading\Interfaces;

use App\Domain\Reading\Entities\UserReadingProgress;

interface UserReadingProgressRepositoryInterface
{
    public function findByUserAndBook(int $userId, int $bookId): ?UserReadingProgress;

    public function save(UserReadingProgress $progress): UserReadingProgress;

    /** @return UserReadingProgress[] */
    public function findAllByUser(int $userId): array;
}
