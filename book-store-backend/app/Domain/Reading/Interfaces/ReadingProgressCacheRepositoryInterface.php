<?php

namespace App\Domain\Reading\Interfaces;

use App\Domain\Reading\ValueObjects\ReadingPosition;

interface ReadingProgressCacheRepositoryInterface
{
    public function get(int $userId, int $bookId): ?ReadingPosition;

    public function set(int $userId, ReadingPosition $position): void;

    public function forget(int $userId, int $bookId): void;
}
