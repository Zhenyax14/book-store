<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reading\UseCases;

use App\Application\Reading\UseCases\StartReading\StartReadingCommand;
use App\Application\Reading\UseCases\StartReading\StartReadingHandler;
use App\Domain\Reading\Entities\ReadingEntry;
use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\Exceptions\InvalidReadingStatusTransitionException;
use App\Domain\Reading\Exceptions\ReadingEntryNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeReadingListRepository;

final class StartReadingTest extends TestCase
{
    private FakeReadingListRepository $entries;

    private StartReadingHandler       $handler;

    protected function setUp(): void
    {
        $this->entries = new FakeReadingListRepository();
        $this->handler = new StartReadingHandler($this->entries);
    }

    public function test_transitions_to_reading_status(): void
    {
        $this->seedEntry(1, 10, ReadingStatusEnum::WANT_TO_READ);

        $result = $this->handler->handle(new StartReadingCommand(userId: 1, bookId: 10, totalPages: 300));

        $this->assertSame(ReadingStatusEnum::READING, $result->entry->status);
        $this->assertSame(300, $result->entry->totalPages);
        $this->assertSame(0, $result->entry->currentPage);
        $this->assertNotNull($result->entry->startedAt);
    }

    public function test_throws_when_entry_not_found(): void
    {
        $this->expectException(ReadingEntryNotFoundException::class);

        $this->handler->handle(new StartReadingCommand(userId: 1, bookId: 99, totalPages: 100));
    }

    public function test_throws_on_invalid_transition_from_finished(): void
    {
        $this->seedEntry(1, 10, ReadingStatusEnum::FINISHED);

        $this->expectException(InvalidReadingStatusTransitionException::class);

        $this->handler->handle(new StartReadingCommand(userId: 1, bookId: 10, totalPages: 100));
    }

    public function test_can_resume_from_dropped(): void
    {
        $this->seedEntry(1, 10, ReadingStatusEnum::DROPPED);

        $result = $this->handler->handle(new StartReadingCommand(userId: 1, bookId: 10, totalPages: 200));

        $this->assertSame(ReadingStatusEnum::READING, $result->entry->status);
    }

    private function seedEntry(int $userId, int $bookId, ReadingStatusEnum $status): void
    {
        $this->entries->save(new ReadingEntry(
            userId: $userId,
            bookId: $bookId,
            status: $status,
            currentPage: 0,
        ));
    }
}
