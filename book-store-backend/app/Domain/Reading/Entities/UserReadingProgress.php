<?php

declare(strict_types=1);

namespace App\Domain\Reading\Entities;

use App\Domain\Reading\ValueObjects\ReadingPosition;
use DateTimeImmutable;

final readonly class UserReadingProgress
{
    public function __construct(
        public ?int               $id,
        public int                $userId,
        public int                $bookId,
        public int                $totalPages,
        public ?ReadingPosition   $position,
        public float              $completionPercentage,
        public bool               $isFinished,
        public ?DateTimeImmutable $lastReadAt,
        public ?DateTimeImmutable $finishedAt,
    ) {}

    public static function initiate(int $userId, int $bookId, int $totalPages): self
    {
        return new self(
            id: null,
            userId: $userId,
            bookId: $bookId,
            totalPages: $totalPages,
            position: null,
            completionPercentage: 0.0,
            isFinished: false,
            lastReadAt: null,
            finishedAt: null,
        );
    }

    public function withPosition(ReadingPosition $position, int $totalPages): self
    {
        $percentage = $totalPages > 0
            ? round($position->globalPageNumber / $totalPages * 100, 2)
            : 0.0;

        $isFinished = $percentage >= 100.0;

        return new self(
            id: $this->id,
            userId: $this->userId,
            bookId: $this->bookId,
            totalPages: $totalPages,
            position: $position,
            completionPercentage: $percentage,
            isFinished: $isFinished,
            lastReadAt: new DateTimeImmutable(),
            finishedAt: $isFinished && ! $this->isFinished
                ? new DateTimeImmutable()
                : $this->finishedAt,
        );
    }
}
