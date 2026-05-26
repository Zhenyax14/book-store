<?php

declare(strict_types=1);

namespace App\Application\Notification\UseCases\MarkAllNotificationsAsRead;

use App\Domain\Notification\Interfaces\NotificationRepositoryInterface;

final readonly class MarkAllNotificationsAsReadHandler
{
    public function __construct(
        private NotificationRepositoryInterface $notifications,
    ) {}

    public function handle(MarkAllNotificationsAsReadCommand $command): void
    {
        $this->notifications->markAllAsRead($command->userId);
    }
}
