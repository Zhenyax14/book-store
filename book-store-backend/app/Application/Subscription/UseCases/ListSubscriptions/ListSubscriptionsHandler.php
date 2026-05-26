<?php

declare(strict_types=1);

namespace App\Application\Subscription\UseCases\ListSubscriptions;

use App\Domain\Subscription\Interfaces\UserSubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObjects\SubscriptionFilter;

final readonly class ListSubscriptionsHandler
{
    public function __construct(
        private UserSubscriptionRepositoryInterface $subscriptions,
    ) {}

    public function handle(ListSubscriptionsCommand $command): ListSubscriptionsResult
    {

        $filter = new SubscriptionFilter(
            perPage: $command->perPage,
            page: $command->page,
            search: $command->search,
            status: $command->status,
        );

        return new ListSubscriptionsResult(
            collection: $this->subscriptions->findAll($filter),
        );
    }
}
