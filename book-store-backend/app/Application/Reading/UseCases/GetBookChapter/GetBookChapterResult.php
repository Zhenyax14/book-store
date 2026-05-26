<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetBookChapter;

use App\Domain\Reading\Entities\BookChapter;

final readonly class GetBookChapterResult
{
    public function __construct(
        public BookChapter $chapter,
    ) {}
}
