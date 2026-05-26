<?php

namespace App\Application\Catalog\Interfaces;

use App\Application\Catalog\DTOs\BookSearchQuery;
use App\Application\Catalog\DTOs\BookSearchResult;
use App\Domain\Catalog\Entities\Book;

interface BookSearchIndexInterface
{
    public function index(Book $book): void;

    public function delete(int $bookId): void;

    public function search(BookSearchQuery $query): BookSearchResult;

    public function bulkIndex(array $books): void;

    public function reindex(array $books): void;
}
