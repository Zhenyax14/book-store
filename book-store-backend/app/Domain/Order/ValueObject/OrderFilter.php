<?php

declare(strict_types=1);

namespace App\Domain\Order\ValueObject;

final readonly class OrderFilter
{
    public function __construct(
        public int     $perPage    = 20,
        public int     $page       = 1,
        public ?string $search     = null,
        public ?string $dateFrom   = null,
        public ?string $dateTo     = null,
    ) {}
}
