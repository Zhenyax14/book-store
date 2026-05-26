<?php

declare(strict_types=1);

namespace App\Domain\Reading\ValueObjects;

final readonly class ReadingHistoryItem
{
    public function __construct(
        public int                 $sessionId,
        public int                 $bookId,
        public int                 $pagesRead,
        public int                 $durationSeconds,
        public \DateTimeImmutable  $startedAt,
        public ?\DateTimeImmutable $endedAt,
        public float               $completion,
    ) {}
}
