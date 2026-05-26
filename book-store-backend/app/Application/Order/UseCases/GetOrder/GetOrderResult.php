<?php

declare(strict_types=1);

namespace App\Application\Order\UseCases\GetOrder;

use App\Domain\Order\ValueObject\OrderSummary;

final readonly class GetOrderResult
{
    public function __construct(
        public OrderSummary $order,
    ) {}
}
