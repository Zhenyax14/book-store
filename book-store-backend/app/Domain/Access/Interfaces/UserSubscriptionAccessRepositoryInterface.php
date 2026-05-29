<?php

declare(strict_types=1);

namespace App\Domain\Access\Interfaces;

use App\Domain\Access\Entities\UserSubscription;

interface UserSubscriptionAccessRepositoryInterface
{
    public function findActiveByUser(int $userId): ?UserSubscription;

    public function saveAccess(UserSubscription $subscription): UserSubscription;
}
