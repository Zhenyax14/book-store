<?php

namespace Tests\Fakes;

use App\Domain\Reading\Entities\UserReadingProgress;
use App\Domain\Reading\Interfaces\UserReadingProgressRepositoryInterface;
use PHPUnit\Framework\Assert;

final class FakeUserReadingProgressRepository implements UserReadingProgressRepositoryInterface
{
    /** @var array<string, UserReadingProgress> */
    private array $store = [];

    private int $nextId = 1;

    public function findByUserAndBook(int $userId, int $bookId): ?UserReadingProgress
    {
        return $this->store[$this->key($userId, $bookId)] ?? null;
    }

    public function save(UserReadingProgress $progress): UserReadingProgress
    {
        $id = $progress->id ?? $this->nextId++;

        $saved = new UserReadingProgress(
            id: $id,
            userId: $progress->userId,
            bookId: $progress->bookId,
            totalPages: $progress->totalPages,
            position: $progress->position,
            completionPercentage: $progress->completionPercentage,
            isFinished: $progress->isFinished,
            lastReadAt: $progress->lastReadAt,
            finishedAt: $progress->finishedAt,
        );

        $this->store[$this->key($progress->userId, $progress->bookId)] = $saved;

        return $saved;
    }

    public function findAllByUser(int $userId): array
    {
        return array_values(
            array_filter(
                $this->store,
                static fn(UserReadingProgress $p) => $p->userId === $userId,
            ),
        );
    }

    public function assertSavedForBook(int $userId, int $bookId): void
    {
        Assert::assertArrayHasKey(
            $this->key($userId, $bookId),
            $this->store,
            "Expected progress for user={$userId}, book={$bookId} to be saved.",
        );
    }

    public function assertCompletionPercentage(int $userId, int $bookId, float $expected): void
    {
        $progress = $this->store[$this->key($userId, $bookId)] ?? null;
        Assert::assertNotNull($progress, "No progress found for user={$userId}, book={$bookId}.");
        Assert::assertEquals($expected, $progress->completionPercentage);
    }

    public function assertFinished(int $userId, int $bookId): void
    {
        $progress = $this->store[$this->key($userId, $bookId)] ?? null;
        Assert::assertNotNull($progress);
        Assert::assertTrue($progress->isFinished, 'Expected reading to be finished.');
    }

    private function key(int $userId, int $bookId): string
    {
        return "{$userId}:{$bookId}";
    }
}
