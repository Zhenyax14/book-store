<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\GetPopularBooks;

use App\Domain\Catalog\ValueObjects\BookCollection;

final readonly class GetPopularBooksResult
{
    public function __construct(
        public BookCollection $collection,
    ) {}
}
