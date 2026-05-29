<?php

declare(strict_types=1);

namespace App\Domain\Reading\Events;

final readonly class BookReadingFinished
{
    public function __construct(
        public int    $userId,
        public int    $bookId,
        public string $bookTitle,
    ) {}
}
