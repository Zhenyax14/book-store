<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\AddBookToList;

final readonly class AddBookToListCommand
{
    public function __construct(
        public int $userId,
        public int $bookId,
    ) {}
}
