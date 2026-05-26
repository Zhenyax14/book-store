<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\SearchBooks;

use App\Application\Catalog\DTOs\BookSearchResult;
use App\Application\Catalog\Interfaces\BookSearchIndexInterface;

final readonly class SearchBooksHandler
{
    public function __construct(
        private BookSearchIndexInterface $searchIndex,
    ) {}

    public function handle(SearchBooksCommand $command): BookSearchResult
    {
        return $this->searchIndex->search($command->toQuery());
    }
}
