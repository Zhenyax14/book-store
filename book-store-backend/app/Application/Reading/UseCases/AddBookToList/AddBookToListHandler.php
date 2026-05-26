<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\AddBookToList;

use App\Domain\Reading\Entities\ReadingEntry;
use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\Exceptions\ReadingEntryAlreadyExistsException;
use App\Domain\Reading\Interfaces\ReadingListRepositoryInterface;

final readonly class AddBookToListHandler
{
    public function __construct(
        private ReadingListRepositoryInterface $entries,
    ) {}

    public function handle(AddBookToListCommand $command): AddBookToListResult
    {
        if ($this->entries->existsForUser($command->userId, $command->bookId)) {
            throw new ReadingEntryAlreadyExistsException($command->bookId);
        }

        $entry = new ReadingEntry(
            userId: $command->userId,
            bookId: $command->bookId,
            status: ReadingStatusEnum::WANT_TO_READ,
            currentPage: 0,
        );

        $saved = $this->entries->save($entry);

        return new AddBookToListResult(entry: $saved);
    }
}
