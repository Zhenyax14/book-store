<?php

namespace App\Application\Reading\UseCases\SaveReadingProgress;

use App\Domain\Reading\Entities\UserReadingProgress;
use App\Domain\Reading\Events\BookReadingFinished;
use App\Domain\Reading\Interfaces\ReadingProgressCacheRepositoryInterface;
use App\Domain\Reading\Interfaces\UserReadingProgressRepositoryInterface;
use App\Domain\Reading\ValueObjects\ReadingPosition;
use App\Application\Shared\Interfaces\EventDispatcherInterface;

final readonly class SaveReadingProgressHandler
{
    public function __construct(
        private UserReadingProgressRepositoryInterface $progressRepository,
        private ReadingProgressCacheRepositoryInterface $cache,
        private EventDispatcherInterface                $dispatcher,
    ) {}

    public function handle(SaveReadingProgressCommand $command): SaveReadingProgressResult
    {
        $position = new ReadingPosition(
            bookId: $command->bookId,
            chapterId: $command->chapterId,
            pageId: $command->pageId,
            globalPageNumber: $command->globalPageNumber,
            scrollPosition: $command->scrollPosition,
        );

        $progress = $this->progressRepository
            ->findByUserAndBook($command->userId, $command->bookId)
            ?? UserReadingProgress::initiate($command->userId, $command->bookId, $command->totalPages);

        $wasFinished = $progress?->isFinished ?? false;
        $updated = $progress->withPosition($position, $command->totalPages);

        $this->progressRepository->save($updated);
        $this->cache->set($command->userId, $position);

        if ($updated->isFinished && ! $wasFinished) {
            $this->dispatcher->dispatch(new BookReadingFinished(
                userId: $command->userId,
                bookId: $command->bookId,
                bookTitle: $command->bookTitle,
            ));
        }

        return new SaveReadingProgressResult(
            completionPercentage: $updated->completionPercentage,
            isFinished: $updated->isFinished,
        );
    }
}
