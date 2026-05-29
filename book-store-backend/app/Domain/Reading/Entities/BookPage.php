<?php

declare(strict_types=1);

namespace App\Domain\Reading\Entities;

use App\Domain\Reading\Enums\ContentFormatEnum;

final readonly class BookPage
{
    public function __construct(
        public ?int              $id = null,
        public int               $chapterId,
        public int               $number,
        public int               $globalNumber,
        public string            $content,
        public ContentFormatEnum $contentFormat,
        public int               $wordCount,
    ) {}
}
