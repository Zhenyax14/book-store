<?php

declare(strict_types=1);

namespace App\Application\Subscription\UseCases\ListSubscriptions;

use App\Domain\Subscription\ValueObjects\SubscriptionCollection;

final readonly class ListSubscriptionsResult
{
    public function __construct(
        public SubscriptionCollection $collection,
    ) {}
}
