<?php

declare(strict_types=1);

namespace App\Application\Order\UseCases\ListOrders;

use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Order\ValueObject\OrderFilter;

final readonly class ListOrdersHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
    ) {}

    public function handle(ListOrdersCommand $command): ListOrdersResult
    {
        $filter = new OrderFilter(
            perPage: $command->perPage,
            page: $command->page,
            search: $command->search,
            dateFrom: $command->dateFrom,
            dateTo: $command->dateTo,
        );
        $collection = $this->orders->findOrders($filter);

        return new ListOrdersResult(collection: $collection);
    }
}
