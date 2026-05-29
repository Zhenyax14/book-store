<?php

declare(strict_types=1);

namespace App\Domain\Reading\Entities;

final readonly class Bookmark
{
    public function __construct(
        public ?int $id = null,
        public int $userId,
        public int $bookId,
        public int $chapterId,
        public int $pageId,
        public string $label,
        public string $color,
    ) {}
}
