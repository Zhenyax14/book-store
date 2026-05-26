<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\UpdateBook;

use App\Domain\Catalog\Entities\Book;

final readonly class UpdateBookResult
{
    public function __construct(
        public Book $book,
    ) {}
}
