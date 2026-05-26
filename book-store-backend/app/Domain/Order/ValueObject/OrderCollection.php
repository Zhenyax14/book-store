<?php

declare(strict_types=1);

namespace App\Domain\Order\ValueObject;

final readonly class OrderCollection
{
    /** @param OrderSummary[] $items */
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
