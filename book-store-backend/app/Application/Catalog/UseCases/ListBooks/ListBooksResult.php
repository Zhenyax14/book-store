<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\ListBooks;

use App\Domain\Catalog\ValueObjects\BookCollection;

final readonly class ListBooksResult
{
    public function __construct(
        public BookCollection $collection,
    ) {}
}
