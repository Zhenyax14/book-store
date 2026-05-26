<?php

declare(strict_types=1);

namespace App\Domain\Order\Interfaces;

use App\Domain\Order\ValueObject\OrderCollection;
use App\Domain\Order\ValueObject\OrderFilter;
use App\Domain\Order\ValueObject\OrderSummary;

interface OrderRepositoryInterface
{
    public function findOrders(OrderFilter $filter): OrderCollection;

    public function findById(int $cartId): OrderSummary;
}
