<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetBookChapter;

use App\Domain\Reading\Exceptions\ChapterNotFoundException;
use App\Domain\Reading\Interfaces\BookChapterRepositoryInterface;

final readonly class GetBookChapterHandler
{
    public function __construct(
        private BookChapterRepositoryInterface $chapters,
    ) {}

    public function handle(GetBookChapterCommand $command): GetBookChapterResult
    {
        $result = $this->chapters->findById($command->chapterId);

        if ( ! $result) {
            throw new ChapterNotFoundException($command->chapterId);
        }

        return new GetBookChapterResult($result);
    }
}
