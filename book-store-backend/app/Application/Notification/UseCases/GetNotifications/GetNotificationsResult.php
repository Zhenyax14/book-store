<?php

declare(strict_types=1);

namespace App\Application\Notification\UseCases\GetNotifications;

use App\Domain\Notification\ValueObjects\NotificationCollection;

final readonly class GetNotificationsResult
{
    public function __construct(
        public NotificationCollection $collection,
    ) {}
}
