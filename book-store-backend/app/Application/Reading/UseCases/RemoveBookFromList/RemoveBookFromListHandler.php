<?php

namespace App\Application\Reading\UseCases\RemoveBookFromList;

use App\Domain\Reading\Exceptions\ReadingEntryNotFoundException;
use App\Domain\Reading\Interfaces\ReadingListRepositoryInterface;

final readonly class RemoveBookFromListHandler
{
    public function __construct(
        private ReadingListRepositoryInterface $entries,
    ) {}

    public function handle(RemoveBookFromListCommand $command): void
    {
        $entry = $this->entries->findEntry($command->userId, $command->bookId);

        if ( ! $entry) {
            throw new ReadingEntryNotFoundException($command->userId, $command->bookId);
        }

        $this->entries->delete($command->userId, $command->bookId);
    }
}
