<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetBook;

use App\Domain\Reading\Entities\Book;

final readonly class GetBookResult
{
    public function __construct(
        public Book $book,
    ) {}
}
