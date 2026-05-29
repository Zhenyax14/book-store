<?php

declare(strict_types=1);

namespace App\Domain\Subscription\ValueObjects;

use App\Domain\Subscription\Entities\Subscription;

final readonly class SubscriptionCollection
{
    public function __construct(
        /** @var Subscription[] */
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
