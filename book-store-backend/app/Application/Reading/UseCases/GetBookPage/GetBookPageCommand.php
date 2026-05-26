<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetBookPage;

final readonly class GetBookPageCommand
{
    public function __construct(
        public int $bookId,
        public int $pageId,
        public int $userId,
    ) {}
}
