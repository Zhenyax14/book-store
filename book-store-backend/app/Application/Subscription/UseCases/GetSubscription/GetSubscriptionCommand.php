<?php

declare(strict_types=1);

namespace App\Application\Subscription\UseCases\GetSubscription;

final readonly class GetSubscriptionCommand
{
    public function __construct(
        public int $subscriptionId,
    ) {}
}
