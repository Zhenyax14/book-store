<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Interfaces;

use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\ValueObjects\SubscriptionCollection;
use App\Domain\Subscription\ValueObjects\SubscriptionFilter;

interface UserSubscriptionRepositoryInterface
{
    public function findById(int $id): Subscription;

    public function findAll(SubscriptionFilter $filter): SubscriptionCollection;
}
