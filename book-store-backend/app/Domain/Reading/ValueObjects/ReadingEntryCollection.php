<?php

declare(strict_types=1);

namespace App\Domain\Reading\ValueObjects;

use App\Domain\Reading\Entities\ReadingEntry;

final readonly class ReadingEntryCollection
{
    /** @param ReadingEntry[] $items */
    public function __construct(
        public array $items,
        public int   $total,
        public int   $perPage,
        public int   $currentPage,
    ) {}

    public function totalPages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }
}
