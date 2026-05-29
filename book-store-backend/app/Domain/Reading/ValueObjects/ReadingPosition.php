<?php

declare(strict_types=1);

namespace App\Domain\Reading\ValueObjects;

final readonly class ReadingPosition
{
    public function __construct(
        public int $bookId,
        public int $chapterId,
        public int $pageId,
        public int $globalPageNumber,
        public int $scrollPosition,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            bookId: $data['book_id'],
            chapterId: $data['chapter_id'],
            pageId: $data['page_id'],
            globalPageNumber: $data['global_page_number'],
            scrollPosition: $data['scroll_position'],
        );
    }

    public function toArray(): array
    {
        return [
            'book_id'            => $this->bookId,
            'chapter_id'         => $this->chapterId,
            'page_id'            => $this->pageId,
            'global_page_number' => $this->globalPageNumber,
            'scroll_position'    => $this->scrollPosition,
        ];
    }
}
