<?php

declare(strict_types=1);

namespace App\Application\Subscription\UseCases\GetSubscription;

use App\Domain\Subscription\Interfaces\UserSubscriptionRepositoryInterface;

final readonly class GetSubscriptionHandler
{
    public function __construct(
        private UserSubscriptionRepositoryInterface $subscriptions,
    ) {}

    public function handle(GetSubscriptionCommand $command): GetSubscriptionResult
    {
        return new GetSubscriptionResult(
            $this->subscriptions->findById($command->subscriptionId),
        );
    }
}
