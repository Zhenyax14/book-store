<?php

namespace App\Application\Catalog\UseCases\CreateBook;

use App\Domain\Catalog\Entities\Book;

final readonly class CreateBookResult
{
    public function __construct(
        public Book $book,
    ) {}
}
