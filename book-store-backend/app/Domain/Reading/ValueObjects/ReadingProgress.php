<?php

declare(strict_types=1);

namespace App\Domain\Reading\ValueObjects;

final readonly class ReadingProgress
{
    public function __construct(
        public int $bookId,
        public int $totalPages,
        public int $readPages,
    ) {}

    public function percentage(): float
    {
        if (0 === $this->totalPages) {
            return 0.0;
        }

        return round(($this->readPages / $this->totalPages) * 100, 2);
    }

    public function isFinished(): bool
    {
        return $this->readPages >= $this->totalPages;
    }

    public function remainingPages(): int
    {
        return max(0, $this->totalPages - $this->readPages);
    }
}
