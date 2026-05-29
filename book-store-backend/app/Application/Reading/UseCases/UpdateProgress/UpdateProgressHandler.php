<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\UpdateProgress;

use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\Exceptions\InvalidReadingStatusTransitionException;
use App\Domain\Reading\Exceptions\ReadingEntryNotFoundException;
use App\Domain\Reading\Interfaces\ReadingListRepositoryInterface;

final readonly class UpdateProgressHandler
{
    public function __construct(
        private ReadingListRepositoryInterface $entries,
    ) {}

    public function handle(UpdateProgressCommand $command): UpdateProgressResult
    {
        $entry = $this->entries->findEntry($command->userId, $command->bookId);

        if ( ! $entry) {
            throw new ReadingEntryNotFoundException($command->userId, $command->bookId);
        }

        if (ReadingStatusEnum::READING !== $entry->status) {
            throw new InvalidReadingStatusTransitionException($entry->status, ReadingStatusEnum::READING);
        }

        $saved = $this->entries->save($entry->updateProgress($command->currentPage));

        return new UpdateProgressResult(entry: $saved);
    }
}
