<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetBookChapter;

final readonly class GetBookChapterCommand
{
    public function __construct(
        public int $chapterId,
    ) {}
}
