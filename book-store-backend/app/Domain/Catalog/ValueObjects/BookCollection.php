<?php

declare(strict_types=1);

namespace App\Domain\Catalog\ValueObjects;

use App\Domain\Catalog\Entities\Book;

final readonly class BookCollection
{
    public function __construct(
        /** @var Book[] */
        public array $items,
        public int   $total,
        public int   $perPage,
        public int   $currentPage,
    ) {}

    public function totalPages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }
}
