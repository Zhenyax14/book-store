<?php

namespace Tests\Fakes;

use App\Domain\Reading\Entities\ReadingSession;
use App\Domain\Reading\Interfaces\ReadingSessionRepositoryInterface;
use PHPUnit\Framework\Assert;

final class FakeReadingSessionRepository implements ReadingSessionRepositoryInterface
{
    /** @var array<int, ReadingSession> */
    private array $store  = [];

    private int   $nextId = 1;

    public function save(ReadingSession $session): ReadingSession
    {
        $id = $session->id ?? $this->nextId++;

        $saved = new ReadingSession(
            id: $id,
            userId: $session->userId,
            bookId: $session->bookId,
            startPageId: $session->startPageId,
            endPageId: $session->endPageId,
            startedAt: $session->startedAt,
            endedAt: $session->endedAt,
            pagesRead: $session->pagesRead,
            durationSeconds: $session->durationSeconds,
        );

        $this->store[$id] = $saved;

        return $saved;
    }

    public function findById(int $id): ?ReadingSession
    {
        return $this->store[$id] ?? null;
    }

    public function findActiveByUser(int $userId, int $bookId): ?ReadingSession
    {
        return array_find($this->store, fn($session) => $session->userId === $userId
            && $session->bookId === $bookId
            && null === $session->endedAt);

    }

    public function findByUser(int $userId): array
    {
        return array_values(
            array_filter(
                $this->store,
                static fn(ReadingSession $s) => $s->userId === $userId,
            ),
        );
    }

    // ── Assertions ────────────────────────────────────────────────

    public function assertSessionStarted(int $userId, int $bookId): void
    {
        $found = array_filter(
            $this->store,
            static fn(ReadingSession $s) => $s->userId === $userId && $s->bookId === $bookId,
        );

        Assert::assertNotEmpty($found, "Expected session for user={$userId}, book={$bookId} to exist.");
    }

    public function assertSessionEnded(int $sessionId): void
    {
        $session = $this->store[$sessionId] ?? null;
        Assert::assertNotNull($session, "Session #{$sessionId} not found.");
        Assert::assertNotNull($session->endedAt, "Expected session #{$sessionId} to be ended.");
    }

    public function assertCount(int $expected): void
    {
        Assert::assertCount($expected, $this->store);
    }
}
