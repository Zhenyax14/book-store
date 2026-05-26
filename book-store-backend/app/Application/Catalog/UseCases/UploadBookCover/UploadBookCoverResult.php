<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\UploadBookCover;

use App\Domain\Catalog\Entities\Book;

final readonly class UploadBookCoverResult
{
    public function __construct(
        public Book $book,
    ) {}
}
