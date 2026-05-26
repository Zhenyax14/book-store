<?php

declare(strict_types=1);

namespace App\Application\Catalog\DTOs;

final readonly class BookSearchResult
{
    public function __construct(
        /** @var BookSearchHit[] */
        public array $hits,
        public int   $total,
        public int   $limit,
        public int   $offset,
        public int   $processingTimeMs,
    ) {}

    public function isEmpty(): bool
    {
        return empty($this->hits);
    }
}
