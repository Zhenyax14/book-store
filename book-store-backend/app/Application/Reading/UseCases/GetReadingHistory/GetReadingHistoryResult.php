<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingHistory;

use App\Domain\Reading\ValueObjects\ReadingHistoryItem;

final readonly class GetReadingHistoryResult
{
    /**
     * @param ReadingHistoryItem[] $items
     */
    public function __construct(
        public array $items,
    ) {}

    public function isEmpty(): bool
    {
        return [] === $this->items;
    }

    public function totalPagesRead(): int
    {
        return array_sum(
            array_map(static fn(ReadingHistoryItem $i) => $i->pagesRead, $this->items),
        );
    }

    public function totalDurationSeconds(): int
    {
        return array_sum(
            array_map(static fn(ReadingHistoryItem $i) => $i->durationSeconds, $this->items),
        );
    }
}
