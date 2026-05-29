<?php

declare(strict_types=1);

namespace App\Domain\Reading\Entities;

use App\Domain\Reading\Enums\ReadingStatusEnum;
use DateTimeImmutable;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property ReadingStatusEnum $status
 * @property int $current_page
 * @property int|null $total_pages
 * @property DateTimeImmutable|null $started_at
 * @property DateTimeImmutable|null $finished_at
 */
final readonly class ReadingEntry
{
    public function __construct(
        public int                $userId,
        public int                $bookId,
        public ReadingStatusEnum  $status,
        public int                $currentPage,
        public ?int               $totalPages = null,
        public ?DateTimeImmutable $startedAt  = null,
        public ?DateTimeImmutable $finishedAt = null,
        public ?int               $id         = null,
    ) {}

    public function isFinished(): bool
    {
        return ReadingStatusEnum::FINISHED === $this->status;
    }

    public function isReading(): bool
    {
        return ReadingStatusEnum::READING === $this->status;
    }

    public function progressPercentage(): ?float
    {
        if (null === $this->totalPages || 0 === $this->totalPages) {
            return null;
        }

        return round(($this->currentPage / $this->totalPages) * 100, 2);
    }

    public function startReading(int $totalPages): self
    {
        return new self(
            userId: $this->userId,
            bookId: $this->bookId,
            status: ReadingStatusEnum::READING,
            currentPage: 0,
            totalPages: $totalPages,
            startedAt: new DateTimeImmutable(),
            finishedAt: $this->finishedAt,
            id: $this->id,
        );
    }

    public function updateProgress(int $currentPage): self
    {
        $isCompleted = null !== $this->totalPages && $currentPage >= $this->totalPages;

        return new self(
            userId: $this->userId,
            bookId: $this->bookId,
            status: $isCompleted ? ReadingStatusEnum::FINISHED : $this->status,
            currentPage: $currentPage,
            totalPages: $this->totalPages,
            startedAt: $this->startedAt,
            finishedAt: $isCompleted ? new DateTimeImmutable() : $this->finishedAt,
            id: $this->id,
        );
    }

    public function drop(): self
    {
        return new self(
            userId: $this->userId,
            bookId: $this->bookId,
            status: ReadingStatusEnum::DROPPED,
            currentPage: $this->currentPage,
            totalPages: $this->totalPages,
            startedAt: $this->startedAt,
            finishedAt: $this->finishedAt,
            id: $this->id,
        );
    }
}
