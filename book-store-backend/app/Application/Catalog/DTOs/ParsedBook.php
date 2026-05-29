<?php

declare(strict_types=1);

namespace App\Application\Catalog\DTOs;

final readonly class ParsedBook
{
    public function __construct(
        public int   $bookId,
        public int   $totalPages,
        /** @var ParsedChapter[] */
        public array $chapters,
    ) {}
}
