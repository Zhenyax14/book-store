<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetBookPage;

use App\Domain\Reading\Exceptions\PageNotFoundException;
use App\Domain\Reading\Interfaces\BookPageRepositoryInterface;
use App\Domain\Reading\Interfaces\UserReadingProgressRepositoryInterface;
use App\Domain\Reading\ValueObjects\ReadingProgress;

final readonly class GetBookPageHandler
{
    public function __construct(
        private BookPageRepositoryInterface            $pages,
        private UserReadingProgressRepositoryInterface $progressRepository,
    ) {}

    public function handle(GetBookPageCommand $command): GetBookPageResult
    {
        $page = $this->pages->findById($command->pageId)
            ?? throw new PageNotFoundException($command->pageId);

        $adjacent = $this->pages->findAdjacentPages($command->pageId);

        $progressRecord = $this->progressRepository
            ->findByUserAndBook($command->userId, $command->bookId);

        $progress = new ReadingProgress(
            bookId: $command->bookId,
            totalPages: $progressRecord?->totalPages ?? 0,
            readPages: $progressRecord?->position?->globalPageNumber ?? 0,
        );

        return new GetBookPageResult(
            page: $page,
            adjacent: $adjacent,
            progress: $progress,
        );
    }
}
