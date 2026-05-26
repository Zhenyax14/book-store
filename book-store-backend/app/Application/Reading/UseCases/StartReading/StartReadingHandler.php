<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\StartReading;

use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\Exceptions\InvalidReadingStatusTransitionException;
use App\Domain\Reading\Exceptions\ReadingEntryNotFoundException;
use App\Domain\Reading\Interfaces\ReadingListRepositoryInterface;

final readonly class StartReadingHandler
{
    public function __construct(
        private ReadingListRepositoryInterface $entries,
    ) {}

    public function handle(StartReadingCommand $command): StartReadingResult
    {
        $entry = $this->entries->findEntry($command->userId, $command->bookId);

        if ( ! $entry) {
            throw new ReadingEntryNotFoundException($command->userId, $command->bookId);
        }

        if ( ! $entry->status->canTransitionTo(ReadingStatusEnum::READING)) {
            throw new InvalidReadingStatusTransitionException($entry->status, ReadingStatusEnum::READING);
        }

        $saved = $this->entries->save($entry->startReading($command->totalPages));

        return new StartReadingResult(entry: $saved);
    }
}
