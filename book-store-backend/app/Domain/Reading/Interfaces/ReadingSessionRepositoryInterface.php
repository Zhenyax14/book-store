<?php

declare(strict_types=1);

namespace App\Domain\Reading\Interfaces;

use App\Domain\Reading\Entities\ReadingSession;

interface ReadingSessionRepositoryInterface
{
    public function save(ReadingSession $session): ReadingSession;

    public function findById(int $id): ?ReadingSession;

    public function findActiveByUser(int $userId, int $bookId): ?ReadingSession;

    /** @return ReadingSession[] */
    public function findByUser(int $userId): array;
}
