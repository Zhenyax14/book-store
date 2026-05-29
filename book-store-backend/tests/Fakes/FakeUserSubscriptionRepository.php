<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Access\Entities\UserSubscription;
use App\Domain\Access\Interfaces\UserSubscriptionAccessRepositoryInterface;
use PHPUnit\Framework\Assert;

final class FakeUserSubscriptionRepository implements UserSubscriptionAccessRepositoryInterface
{
    /** @var UserSubscription[] */
    private array $store = [];

    public function findActiveByUser(int $userId): ?UserSubscription
    {
        return array_find(
            $this->store,
            static fn(UserSubscription $s) => $s->userId === $userId && $s->isActive(),
        );
    }

    public function saveAccess(UserSubscription $subscription): UserSubscription
    {
        $saved = new UserSubscription(
            id: $subscription->id ?? count($this->store) + 1,
            userId: $subscription->userId,
            status: $subscription->status,
            startedAt: $subscription->startedAt,
            expiresAt: $subscription->expiresAt,
            stripeSubscriptionId: $subscription->stripeSubscriptionId,
        );
        $this->store[] = $saved;

        return $saved;
    }

    public function assertCount(int $expected): void
    {
        Assert::assertCount($expected, $this->store);
    }
}
