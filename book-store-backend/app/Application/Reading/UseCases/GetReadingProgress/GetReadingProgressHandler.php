<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingProgress;

use App\Domain\Reading\Interfaces\ReadingProgressCacheRepositoryInterface;
use App\Domain\Reading\Interfaces\UserReadingProgressRepositoryInterface;
use App\Domain\Reading\ValueObjects\ReadingProgress;

final readonly class GetReadingProgressHandler
{
    public function __construct(
        private UserReadingProgressRepositoryInterface $progressRepository,
        private ReadingProgressCacheRepositoryInterface $cache,
    ) {}

    public function handle(GetReadingProgressCommand $command): GetReadingProgressResult
    {
        $cachedPosition = $this->cache->get($command->userId, $command->bookId);

        $record = $this->progressRepository
            ->findByUserAndBook($command->userId, $command->bookId);

        $progress = $record
            ? new ReadingProgress(
                $command->bookId,
                $record->totalPages,
                $record->position?->globalPageNumber ?? 0,
            )
            : new ReadingProgress(
                $command->bookId,
                0,
                0,
            );

        return new GetReadingProgressResult(
            progress: $progress,
            lastPosition: $cachedPosition ?? $record?->position,
            isFinished: $record?->isFinished ?? false,
            lastReadAt: $record?->lastReadAt,
        );
    }
}
