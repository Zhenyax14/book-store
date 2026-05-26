<?php

declare(strict_types=1);

namespace App\Application\Order\UseCases\GetOrder;

final readonly class GetOrderCommand
{
    public function __construct(
        public int $orderId,
    ) {}
}
