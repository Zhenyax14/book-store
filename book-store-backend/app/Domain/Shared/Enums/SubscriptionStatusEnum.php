<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

enum SubscriptionStatusEnum: string
{
    case ACTIVE   = 'active';
    case EXPIRED  = 'expired';
    case CANCELLED = 'cancelled';
}
