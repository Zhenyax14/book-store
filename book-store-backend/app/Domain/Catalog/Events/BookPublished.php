<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Events;

final readonly class BookPublished
{
    public function __construct(
        public int    $bookId,
        public string $bookTitle,
        public string $accessType,
    ) {}
}
