<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reading\UseCases;

use App\Application\Reading\UseCases\GetReadingHistory\GetReadingHistoryCommand;
use App\Application\Reading\UseCases\GetReadingHistory\GetReadingHistoryHandler;
use App\Domain\Reading\Entities\ReadingSession;
use App\Domain\Reading\Entities\UserReadingProgress;
use App\Domain\Reading\ValueObjects\ReadingPosition;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeReadingSessionRepository;
use Tests\Fakes\FakeUserReadingProgressRepository;

final class GetReadingHistoryTest extends TestCase
{
    private FakeReadingSessionRepository      $sessions;

    private FakeUserReadingProgressRepository $progressRepo;

    private GetReadingHistoryHandler          $handler;

    protected function setUp(): void
    {
        $this->sessions     = new FakeReadingSessionRepository();
        $this->progressRepo = new FakeUserReadingProgressRepository();
        $this->handler      = new GetReadingHistoryHandler($this->sessions, $this->progressRepo);
    }

    public function test_returns_empty_result_when_no_sessions(): void
    {
        $result = $this->handler->handle(new GetReadingHistoryCommand(userId: 1));

        $this->assertTrue($result->isEmpty());
        $this->assertEquals(0, $result->totalPagesRead());
        $this->assertEquals(0, $result->totalDurationSeconds());
    }

    public function test_returns_sessions_for_user(): void
    {
        $this->sessions->save($this->makeSession(userId: 1, bookId: 10, pagesRead: 30, duration: 900));
        $this->sessions->save($this->makeSession(userId: 1, bookId: 20, pagesRead: 50, duration: 1500));
        $this->sessions->save($this->makeSession(userId: 2, bookId: 10, pagesRead: 10, duration: 300));

        $result = $this->handler->handle(new GetReadingHistoryCommand(userId: 1));

        $this->assertCount(2, $result->items);
        $this->assertEquals(80, $result->totalPagesRead());
        $this->assertEquals(2400, $result->totalDurationSeconds());
    }

    public function test_enriches_items_with_completion_percentage(): void
    {
        $this->sessions->save($this->makeSession(userId: 1, bookId: 10, pagesRead: 20, duration: 600));

        $progress = UserReadingProgress::initiate(1, 10, 100);
        $position = new ReadingPosition(
            bookId: 10,
            chapterId: 1,
            pageId: 20,
            globalPageNumber: 20,
            scrollPosition: 0,
        );
        $this->progressRepo->save($progress->withPosition($position, 100));

        $result = $this->handler->handle(new GetReadingHistoryCommand(userId: 1));

        $this->assertEqualsWithDelta(20.0, $result->items[0]->completion, 0.01);
    }

    private function makeSession(
        int $userId,
        int $bookId,
        int $pagesRead,
        int $duration,
    ): ReadingSession {
        return new ReadingSession(
            id: null,
            userId: $userId,
            bookId: $bookId,
            startPageId: 1,
            endPageId: $pagesRead,
            startedAt: new \DateTimeImmutable(),
            endedAt: new \DateTimeImmutable(),
            pagesRead: $pagesRead,
            durationSeconds: $duration,
        );
    }
}
