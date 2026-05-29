<?php

declare(strict_types=1);

namespace App\Domain\Access\Entities;

use App\Domain\Shared\Enums\SubscriptionStatusEnum;
use DateTimeImmutable;

final readonly class UserSubscription
{
    public function __construct(
        public ?int                $id,
        public int                 $userId,
        public SubscriptionStatusEnum $status,
        public DateTimeImmutable  $startedAt,
        public DateTimeImmutable  $expiresAt,
        public ?string             $stripeSubscriptionId,
    ) {}

    public function isActive(): bool
    {
        return SubscriptionStatusEnum::ACTIVE === $this->status
            && $this->expiresAt > new DateTimeImmutable();
    }
}
