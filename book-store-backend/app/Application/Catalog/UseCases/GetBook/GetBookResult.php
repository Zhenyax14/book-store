<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\GetBook;

use App\Domain\Catalog\Entities\Book;

final readonly class GetBookResult
{
    public function __construct(
        public Book $book,
        public array $fileLinks,
    ) {}
}
