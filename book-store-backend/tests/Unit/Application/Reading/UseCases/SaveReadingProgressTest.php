<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reading\UseCases;

use App\Application\Reading\UseCases\SaveReadingProgress\SaveReadingProgressCommand;
use App\Application\Reading\UseCases\SaveReadingProgress\SaveReadingProgressHandler;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeEventDispatcher;
use Tests\Fakes\FakeReadingProgressCacheRepository;
use Tests\Fakes\FakeUserReadingProgressRepository;

final class SaveReadingProgressTest extends TestCase
{
    private FakeUserReadingProgressRepository $progressRepo;

    private FakeReadingProgressCacheRepository $cache;

    private SaveReadingProgressHandler $handler;

    protected function setUp(): void
    {
        $this->progressRepo = new FakeUserReadingProgressRepository();
        $this->cache = new FakeReadingProgressCacheRepository();
        $eventDispatcher = new FakeEventDispatcher();
        $this->handler = new SaveReadingProgressHandler($this->progressRepo, $this->cache, $eventDispatcher);
    }

    public function test_saves_new_progress_when_none_exists(): void
    {
        $result = $this->handler->handle(new SaveReadingProgressCommand(
            userId: 1,
            bookId: 10,
            chapterId: 2,
            pageId: 50,
            globalPageNumber: 50,
            scrollPosition: 30,
            totalPages: 200,
            bookTitle: 'Test Book',
        ));

        $this->progressRepo->assertSavedForBook(1, 10);
        $this->assertEqualsWithDelta(25.0, $result->completionPercentage, 0.01);
        $this->assertFalse($result->isFinished);
    }

    public function test_updates_existing_progress(): void
    {
        $this->handler->handle(new SaveReadingProgressCommand(
            userId: 1,
            bookId: 10,
            chapterId: 1,
            pageId: 50,
            globalPageNumber: 40,
            scrollPosition: 0,
            totalPages: 100,
            bookTitle: 'Test Book',
        ));

        $result = $this->handler->handle(new SaveReadingProgressCommand(
            userId: 1,
            bookId: 10,
            chapterId: 2,
            pageId: 80,
            globalPageNumber: 80,
            scrollPosition: 45,
            totalPages: 100,
            bookTitle: 'Test Book',
        ));

        $this->assertEqualsWithDelta(80.0, $result->completionPercentage, 0.01);
        $this->assertFalse($result->isFinished);
    }

    public function test_marks_as_finished_when_last_page_reached(): void
    {
        $result = $this->handler->handle(new SaveReadingProgressCommand(
            userId: 1,
            bookId: 10,
            chapterId: 3,
            pageId: 100,
            globalPageNumber: 100,
            scrollPosition: 100,
            totalPages: 100,
            bookTitle: 'Test Book',
        ));

        $this->assertTrue($result->isFinished);
        $this->assertEqualsWithDelta(100.0, $result->completionPercentage, 0.01);
        $this->progressRepo->assertFinished(1, 10);
    }

    public function test_stores_position_in_cache(): void
    {
        $this->handler->handle(new SaveReadingProgressCommand(
            userId: 1,
            bookId: 10,
            chapterId: 2,
            pageId: 40,
            globalPageNumber: 40,
            scrollPosition: 20,
            totalPages: 200,
            bookTitle: 'Test Book',
        ));

        $this->cache->assertStored(1, 10);
    }
}
