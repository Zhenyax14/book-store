<?php

declare(strict_types=1);

namespace App\Application\Access\UseCases\GrantSubscription;

final readonly class GrantSubscriptionCommand
{
    public function __construct(
        public int     $userId,
        public string  $stripeSubscriptionId,
        public \DateTimeImmutable $expiresAt,
    ) {}
}
