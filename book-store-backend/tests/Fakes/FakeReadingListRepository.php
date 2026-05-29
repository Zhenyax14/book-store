<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Reading\Entities\ReadingEntry;
use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\Interfaces\ReadingListRepositoryInterface;
use App\Domain\Reading\ValueObjects\ReadingEntryCollection;
use PHPUnit\Framework\Assert;

final class FakeReadingListRepository implements ReadingListRepositoryInterface
{
    /** @var array<string, ReadingEntry> */
    private array $store  = [];

    private int   $nextId = 1;

    public function findByUser(int $userId, ?ReadingStatusEnum $status, int $perPage, int $page): ReadingEntryCollection
    {
        $items = array_values(array_filter(
            $this->store,
            static fn(ReadingEntry $e) => $e->userId === $userId
                && (null === $status || $e->status === $status),
        ));

        return new ReadingEntryCollection(
            items: $items,
            total: count($items),
            perPage: $perPage,
            currentPage: $page,
        );
    }

    public function findEntry(int $userId, int $bookId): ?ReadingEntry
    {
        return $this->store[$this->key($userId, $bookId)] ?? null;
    }

    public function save(ReadingEntry $entry): ReadingEntry
    {
        $saved = new ReadingEntry(
            userId: $entry->userId,
            bookId: $entry->bookId,
            status: $entry->status,
            currentPage: $entry->currentPage,
            totalPages: $entry->totalPages,
            startedAt: $entry->startedAt,
            finishedAt: $entry->finishedAt,
            id: $entry->id ?? $this->nextId++,
        );

        $this->store[$this->key($entry->userId, $entry->bookId)] = $saved;

        return $saved;
    }

    public function delete(int $userId, int $bookId): void
    {
        unset($this->store[$this->key($userId, $bookId)]);
    }

    public function existsForUser(int $userId, int $bookId): bool
    {
        return isset($this->store[$this->key($userId, $bookId)]);
    }

    public function assertSaved(int $userId, int $bookId): void
    {
        Assert::assertArrayHasKey(
            $this->key($userId, $bookId),
            $this->store,
            "Expected entry for user={$userId}, book={$bookId} to be saved.",
        );
    }

    public function assertDeleted(int $userId, int $bookId): void
    {
        Assert::assertArrayNotHasKey(
            $this->key($userId, $bookId),
            $this->store,
            "Expected entry for user={$userId}, book={$bookId} to be deleted.",
        );
    }

    public function assertStatus(int $userId, int $bookId, ReadingStatusEnum $expected): void
    {
        $entry = $this->store[$this->key($userId, $bookId)] ?? null;
        Assert::assertNotNull($entry, "Entry for user={$userId}, book={$bookId} not found.");
        Assert::assertSame($expected, $entry->status);
    }

    public function assertCount(int $expected): void
    {
        Assert::assertCount($expected, $this->store);
    }

    private function key(int $userId, int $bookId): string
    {
        return "{$userId}:{$bookId}";
    }
}
