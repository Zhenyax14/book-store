<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingHistory;

use App\Domain\Reading\Interfaces\ReadingSessionRepositoryInterface;
use App\Domain\Reading\Interfaces\UserReadingProgressRepositoryInterface;
use App\Domain\Reading\ValueObjects\ReadingHistoryItem;

final readonly class GetReadingHistoryHandler
{
    public function __construct(
        private ReadingSessionRepositoryInterface      $sessions,
        private UserReadingProgressRepositoryInterface $progressRepository,
    ) {}

    public function handle(GetReadingHistoryCommand $command): GetReadingHistoryResult
    {
        $sessions  = $this->sessions->findByUser($command->userId);
        $allProgress = $this->progressRepository->findAllByUser($command->userId);

        $progressByBook = [];
        foreach ($allProgress as $p) {
            $progressByBook[$p->bookId] = $p;
        }

        $items = array_map(
            static fn($s) => new ReadingHistoryItem(
                sessionId: $s->id,
                bookId: $s->bookId,
                pagesRead: $s->pagesRead,
                durationSeconds: $s->durationSeconds,
                startedAt: $s->startedAt,
                endedAt: $s->endedAt,
                completion: $progressByBook[$s->bookId]?->completionPercentage ?? 0.0,
            ),
            $sessions,
        );

        return new GetReadingHistoryResult($items);
    }
}
