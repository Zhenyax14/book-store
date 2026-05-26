<?php

declare(strict_types=1);

namespace App\Application\Access\UseCases\InitiateSubscription;

final readonly class InitiateSubscriptionCommand
{
    public function __construct(
        public int    $userId,
        public string $priceId,
    ) {}
}
