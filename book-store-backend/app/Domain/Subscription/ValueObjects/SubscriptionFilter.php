<?php

declare(strict_types=1);

namespace App\Domain\Subscription\ValueObjects;

use App\Domain\Shared\Enums\SubscriptionStatusEnum;

final readonly class SubscriptionFilter
{
    public function __construct(
        public int $perPage = 20,
        public int $page = 1,
        public ?string $search = null,
        public ?SubscriptionStatusEnum $status = null,
    ) {}
}
