<?php

declare(strict_types=1);

namespace App\Application\Order\UseCases\ListOrders;

use App\Domain\Order\ValueObject\OrderCollection;

final readonly class ListOrdersResult
{
    public function __construct(
        public OrderCollection $collection,
    ) {}
}
