<?php

declare(strict_types=1);

namespace App\Application\Access\UseCases\InitiateSubscription;

use App\Application\Cart\Interfaces\PaymentGatewayInterface;
use App\Domain\Access\Exceptions\SubscriptionAlreadyActiveException;
use App\Domain\Access\Interfaces\UserSubscriptionAccessRepositoryInterface;

final readonly class InitiateSubscriptionHandler
{
    public function __construct(
        private PaymentGatewayInterface                   $gateway,
        private UserSubscriptionAccessRepositoryInterface $subscriptions,
    ) {}

    public function handle(InitiateSubscriptionCommand $command): string
    {
        $existing = $this->subscriptions->findActiveByUser($command->userId);

        if (null !== $existing) {
            throw new SubscriptionAlreadyActiveException($command->userId);
        }

        return $this->gateway->createSubscriptionSession(
            userId: $command->userId,
            priceId: $command->priceId,
        );
    }
}
