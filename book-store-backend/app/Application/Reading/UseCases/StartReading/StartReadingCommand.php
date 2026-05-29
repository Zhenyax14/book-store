<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\StartReading;

final readonly class StartReadingCommand
{
    public function __construct(
        public int $userId,
        public int $bookId,
        public int $totalPages,
    ) {}

    public static function fromArray(int $userId, int $bookId, array $data): self
    {
        return new self(
            userId: $userId,
            bookId: $bookId,
            totalPages: (int) $data['total_pages'],
        );
    }
}
