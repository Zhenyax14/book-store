<?php

declare(strict_types=1);

namespace App\Application\Notification\UseCases\MarkNotificationAsRead;

final readonly class MarkNotificationAsReadCommand
{
    public function __construct(
        public int    $userId,
        public string $notificationId,
    ) {}
}
