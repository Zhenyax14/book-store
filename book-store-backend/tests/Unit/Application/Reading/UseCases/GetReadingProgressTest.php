<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reading\UseCases;

use App\Application\Reading\UseCases\GetReadingProgress\GetReadingProgressCommand;
use App\Application\Reading\UseCases\GetReadingProgress\GetReadingProgressHandler;
use App\Domain\Reading\Entities\UserReadingProgress;
use App\Domain\Reading\ValueObjects\ReadingPosition;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeReadingProgressCacheRepository;
use Tests\Fakes\FakeUserReadingProgressRepository;

final class GetReadingProgressTest extends TestCase
{
    private FakeUserReadingProgressRepository  $progressRepo;

    private FakeReadingProgressCacheRepository $cache;

    private GetReadingProgressHandler          $handler;

    protected function setUp(): void
    {
        $this->progressRepo = new FakeUserReadingProgressRepository();
        $this->cache        = new FakeReadingProgressCacheRepository();
        $this->handler      = new GetReadingProgressHandler($this->progressRepo, $this->cache);
    }

    public function test_returns_zero_progress_when_no_record_exists(): void
    {
        $result = $this->handler->handle(new GetReadingProgressCommand(userId: 1, bookId: 99));

        $this->assertEquals(0.0, $result->progress->percentage());
        $this->assertFalse($result->isFinished);
        $this->assertNull($result->lastPosition);
    }

    public function test_returns_cached_position_when_available(): void
    {
        $position = new ReadingPosition(bookId: 10, chapterId: 2, pageId: 40, globalPageNumber: 40, scrollPosition: 55);
        $this->cache->set(1, $position);

        $result = $this->handler->handle(new GetReadingProgressCommand(userId: 1, bookId: 10));

        $this->assertNotNull($result->lastPosition);
        $this->assertEquals(55, $result->lastPosition->scrollPosition);
    }

    public function test_returns_db_position_when_cache_is_empty(): void
    {
        $progress = UserReadingProgress::initiate(1, 10, 100);
        $position = new ReadingPosition(bookId: 10, chapterId: 1, pageId: 30, globalPageNumber: 40, scrollPosition: 10);
        $updated  = $progress->withPosition($position, 100);
        $this->progressRepo->save($updated);

        $result = $this->handler->handle(new GetReadingProgressCommand(userId: 1, bookId: 10));

        $this->assertNotNull($result->lastPosition);
        $this->assertEquals(30, $result->lastPosition->pageId);
    }

    public function test_returns_finished_status_from_db(): void
    {
        $progress = UserReadingProgress::initiate(1, 10, 100);
        $position = new ReadingPosition(bookId: 10, chapterId: 5, pageId: 100, globalPageNumber: 100, scrollPosition: 100);
        $this->progressRepo->save($progress->withPosition($position, 100));

        $result = $this->handler->handle(new GetReadingProgressCommand(userId: 1, bookId: 10));

        $this->assertTrue($result->isFinished);
    }
}
