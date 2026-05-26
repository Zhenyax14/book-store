<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Reading\Interfaces\ReadingProgressCacheRepositoryInterface;
use App\Domain\Reading\ValueObjects\ReadingPosition;
use PHPUnit\Framework\Assert;

final class FakeReadingProgressCacheRepository implements ReadingProgressCacheRepositoryInterface
{
    /** @var array<string, ReadingPosition> */
    private array $store = [];

    public function get(int $userId, int $bookId): ?ReadingPosition
    {
        return $this->store[$this->key($userId, $bookId)] ?? null;
    }

    public function set(int $userId, ReadingPosition $position): void
    {
        $this->store[$this->key($userId, $position->bookId)] = $position;
    }

    public function forget(int $userId, int $bookId): void
    {
        unset($this->store[$this->key($userId, $bookId)]);
    }

    public function assertStored(int $userId, int $bookId): void
    {
        Assert::assertArrayHasKey(
            $this->key($userId, $bookId),
            $this->store,
            "Expected reading position for user={$userId}, book={$bookId} to be cached.",
        );
    }

    public function assertForgotten(int $userId, int $bookId): void
    {
        Assert::assertArrayNotHasKey(
            $this->key($userId, $bookId),
            $this->store,
            "Expected reading position for user={$userId}, book={$bookId} to be removed from cache.",
        );
    }

    public function assertEmpty(): void
    {
        Assert::assertEmpty(
            $this->store,
            'Expected cache to be empty.',
        );
    }

    private function key(int $userId, int $bookId): string
    {
        return "{$userId}:{$bookId}";
    }
}
