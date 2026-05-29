<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\RemoveBookFromList;

final readonly class RemoveBookFromListCommand
{
    public function __construct(
        public int $userId,
        public int $bookId,
    ) {}
}
