<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingProgress;

final readonly class GetReadingProgressCommand
{
    public function __construct(
        public int $userId,
        public int $bookId,
    ) {}
}
