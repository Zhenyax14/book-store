<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reading\UseCases;

use App\Application\Reading\UseCases\RemoveBookFromList\RemoveBookFromListCommand;
use App\Application\Reading\UseCases\RemoveBookFromList\RemoveBookFromListHandler;
use App\Domain\Reading\Entities\ReadingEntry;
use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\Exceptions\ReadingEntryNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeReadingListRepository;

final class RemoveBookFromListTest extends TestCase
{
    private FakeReadingListRepository  $entries;

    private RemoveBookFromListHandler  $handler;

    protected function setUp(): void
    {
        $this->entries = new FakeReadingListRepository();
        $this->handler = new RemoveBookFromListHandler($this->entries);
    }

    public function test_removes_entry(): void
    {
        $this->entries->save(new ReadingEntry(
            userId: 1,
            bookId: 10,
            status: ReadingStatusEnum::READING,
            currentPage: 0,
        ));

        $this->handler->handle(new RemoveBookFromListCommand(userId: 1, bookId: 10));

        $this->entries->assertDeleted(1, 10);
    }

    public function test_throws_when_entry_not_found(): void
    {
        $this->expectException(ReadingEntryNotFoundException::class);

        $this->handler->handle(new RemoveBookFromListCommand(userId: 1, bookId: 99));
    }
}
