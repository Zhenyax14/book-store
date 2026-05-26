<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reading\UseCases;

use App\Application\Reading\UseCases\UpdateProgress\UpdateProgressCommand;
use App\Application\Reading\UseCases\UpdateProgress\UpdateProgressHandler;
use App\Domain\Reading\Entities\ReadingEntry;
use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\Exceptions\InvalidReadingStatusTransitionException;
use App\Domain\Reading\Exceptions\ReadingEntryNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeReadingListRepository;

final class UpdateProgressTest extends TestCase
{
    private FakeReadingListRepository $entries;

    private UpdateProgressHandler     $handler;

    protected function setUp(): void
    {
        $this->entries = new FakeReadingListRepository();
        $this->handler = new UpdateProgressHandler($this->entries);
    }

    public function test_updates_current_page(): void
    {
        $this->seedEntry(1, 10, ReadingStatusEnum::READING, totalPages: 300);

        $result = $this->handler->handle(new UpdateProgressCommand(userId: 1, bookId: 10, currentPage: 150));

        $this->assertSame(150, $result->entry->currentPage);
        $this->assertSame(ReadingStatusEnum::READING, $result->entry->status);
    }

    public function test_auto_finishes_on_last_page(): void
    {
        $this->seedEntry(1, 10, ReadingStatusEnum::READING, totalPages: 300);

        $result = $this->handler->handle(new UpdateProgressCommand(userId: 1, bookId: 10, currentPage: 300));

        $this->assertSame(ReadingStatusEnum::FINISHED, $result->entry->status);
        $this->assertNotNull($result->entry->finishedAt);
    }

    public function test_throws_when_entry_not_found(): void
    {
        $this->expectException(ReadingEntryNotFoundException::class);

        $this->handler->handle(new UpdateProgressCommand(userId: 1, bookId: 99, currentPage: 10));
    }

    public function test_throws_when_not_in_reading_status(): void
    {
        $this->seedEntry(1, 10, ReadingStatusEnum::WANT_TO_READ);

        $this->expectException(InvalidReadingStatusTransitionException::class);

        $this->handler->handle(new UpdateProgressCommand(userId: 1, bookId: 10, currentPage: 10));
    }

    private function seedEntry(int $userId, int $bookId, ReadingStatusEnum $status, int $totalPages = 0): void
    {
        $this->entries->save(new ReadingEntry(
            userId: $userId,
            bookId: $bookId,
            status: $status,
            currentPage: 0,
            totalPages: $totalPages ?: null,
        ));
    }
}
