<?php

declare(strict_types=1);

namespace App\Application\Subscription\UseCases\GetSubscription;

use App\Domain\Subscription\Entities\Subscription;

final readonly class GetSubscriptionResult
{
    public function __construct(
        public Subscription $subscription,
    ) {}
}
