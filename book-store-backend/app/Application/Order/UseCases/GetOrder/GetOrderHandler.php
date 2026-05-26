<?php

declare(strict_types=1);

namespace App\Application\Order\UseCases\GetOrder;

use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;

final readonly class GetOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
    ) {}

    public function handle(GetOrderCommand $command): GetOrderResult
    {
        $order = $this->orders->findById($command->orderId);

        if ( ! $order) {
            throw new OrderNotFoundException($command->orderId);
        }

        return new GetOrderResult($order);
    }
}
