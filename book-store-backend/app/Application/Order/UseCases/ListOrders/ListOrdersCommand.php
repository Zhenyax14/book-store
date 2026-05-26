<?php

declare(strict_types=1);

namespace App\Application\Order\UseCases\ListOrders;

final readonly class ListOrdersCommand
{
    public function __construct(
        public int     $perPage  = 20,
        public int     $page     = 1,
        public ?string $search   = null,
        public ?string $dateFrom = null,
        public ?string $dateTo   = null,
    ) {}
}
