<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\UpdateProgress;

final readonly class UpdateProgressCommand
{
    public function __construct(
        public int $userId,
        public int $bookId,
        public int $currentPage,
    ) {}

    public static function fromArray(int $userId, int $bookId, array $data): self
    {
        return new self(
            userId: $userId,
            bookId: $bookId,
            currentPage: (int) $data['current_page'],
        );
    }
}
