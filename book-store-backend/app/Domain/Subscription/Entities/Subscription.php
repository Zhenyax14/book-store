<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Entities;

use App\Domain\Shared\Enums\SubscriptionStatusEnum;

final readonly class Subscription
{
    public function __construct(
        public ?int $id = null,
        public int $userId,
        public SubscriptionStatusEnum $status,
        public string $stripeSubscriptionId,
        public \DateTimeImmutable $startedAt,
        public \DateTimeImmutable $expiresAt,
    ) {}

    public function isActive(): bool
    {
        return SubscriptionStatusEnum::ACTIVE === $this->status;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt <= new \DateTimeImmutable();
    }
}
