<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reading\UseCases;

use App\Application\Reading\UseCases\AddBookToList\AddBookToListCommand;
use App\Application\Reading\UseCases\AddBookToList\AddBookToListHandler;
use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\Exceptions\ReadingEntryAlreadyExistsException;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeReadingListRepository;

final class AddBookToListTest extends TestCase
{
    private FakeReadingListRepository $entries;

    private AddBookToListHandler      $handler;

    protected function setUp(): void
    {
        $this->entries = new FakeReadingListRepository();
        $this->handler = new AddBookToListHandler($this->entries);
    }

    public function test_adds_entry_with_want_to_read_status(): void
    {
        $result = $this->handler->handle(new AddBookToListCommand(userId: 1, bookId: 10));

        $this->assertSame(ReadingStatusEnum::WANT_TO_READ, $result->entry->status);
        $this->entries->assertSaved(1, 10);
    }

    public function test_assigns_id_after_save(): void
    {
        $result = $this->handler->handle(new AddBookToListCommand(userId: 1, bookId: 10));

        $this->assertNotNull($result->entry->id);
    }

    public function test_throws_when_book_already_in_list(): void
    {
        $this->handler->handle(new AddBookToListCommand(userId: 1, bookId: 10));

        $this->expectException(ReadingEntryAlreadyExistsException::class);

        $this->handler->handle(new AddBookToListCommand(userId: 1, bookId: 10));
    }

    public function test_different_users_can_add_same_book(): void
    {
        $this->handler->handle(new AddBookToListCommand(userId: 1, bookId: 10));
        $this->handler->handle(new AddBookToListCommand(userId: 2, bookId: 10));

        $this->entries->assertCount(2);
    }
}
