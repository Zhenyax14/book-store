<?php

declare(strict_types=1);

namespace App\Domain\Reading\Entities;

final readonly class ReadingSession
{
    public function __construct(
        public ?int                $id,
        public int                 $userId,
        public int                 $bookId,
        public ?int                $startPageId,
        public ?int                $endPageId,
        public \DateTimeImmutable  $startedAt,
        public ?\DateTimeImmutable $endedAt,
        public int                 $pagesRead,
        public int                 $durationSeconds,
    ) {}

    public static function begin(int $userId, int $bookId, ?int $startPageId): self
    {
        return new self(
            id: null,
            userId: $userId,
            bookId: $bookId,
            startPageId: $startPageId,
            endPageId: null,
            startedAt: new \DateTimeImmutable(),
            endedAt: null,
            pagesRead: 0,
            durationSeconds: 0,
        );
    }

    public function end(int $endPageId, int $durationSeconds): self
    {
        $pagesRead = $this->startPageId
            ? max(0, $endPageId - $this->startPageId)
            : 0;

        return new self(
            id: $this->id,
            userId: $this->userId,
            bookId: $this->bookId,
            startPageId: $this->startPageId,
            endPageId: $endPageId,
            startedAt: $this->startedAt,
            endedAt: new \DateTimeImmutable(),
            pagesRead: $pagesRead,
            durationSeconds: $durationSeconds,
        );
    }
}
