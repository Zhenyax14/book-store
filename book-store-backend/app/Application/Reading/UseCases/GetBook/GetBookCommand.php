<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetBook;

final readonly class GetBookCommand
{
    public function __construct(
        public int $bookId,
        public int $userId,
    ) {}
}
