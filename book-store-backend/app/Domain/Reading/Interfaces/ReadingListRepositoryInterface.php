<?php

declare(strict_types=1);

namespace App\Domain\Reading\Interfaces;

use App\Domain\Reading\Entities\ReadingEntry;
use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\ValueObjects\ReadingEntryCollection;

interface ReadingListRepositoryInterface
{
    public function findByUser(int $userId, ?ReadingStatusEnum $status, int $perPage, int $page): ReadingEntryCollection;

    public function findEntry(int $userId, int $bookId): ?ReadingEntry;

    public function save(ReadingEntry $entry): ReadingEntry;

    public function delete(int $userId, int $bookId): void;

    public function existsForUser(int $userId, int $bookId): bool;
}
