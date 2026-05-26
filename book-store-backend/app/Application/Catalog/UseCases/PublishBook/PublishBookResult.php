<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\PublishBook;

use App\Domain\Catalog\Entities\Book;

final readonly class PublishBookResult
{
    public function __construct(public Book $book) {}
}
