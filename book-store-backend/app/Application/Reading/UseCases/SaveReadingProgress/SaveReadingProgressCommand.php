<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\SaveReadingProgress;

final readonly class SaveReadingProgressCommand
{
    public function __construct(
        public int $userId,
        public int $bookId,
        public int $chapterId,
        public int $pageId,
        public int $globalPageNumber,
        public int $scrollPosition,
        public int $totalPages,
        public string $bookTitle,
    ) {}
}
