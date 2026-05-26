<?php

declare(strict_types=1);

namespace App\Domain\Reading\Entities;

final readonly class BookChapter
{
    public function __construct(
        public ?int    $id = null,
        public int     $bookId,
        public ?int    $volumeId = null,
        public int     $number,
        public string  $title,
        public string  $slug,
        public int     $readingTimeMinutes,
        public bool    $isPublished,
        public array   $pageIds,
    ) {}

    public function isAccessible(): bool
    {
        return $this->isPublished;
    }
}
