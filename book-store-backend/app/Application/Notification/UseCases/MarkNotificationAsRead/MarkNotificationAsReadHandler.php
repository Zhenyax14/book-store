<?php

namespace App\Application\Notification\UseCases\MarkNotificationAsRead;

use App\Domain\Notification\Exceptions\NotificationNotFoundException;
use App\Domain\Notification\Interfaces\NotificationRepositoryInterface;

final readonly class MarkNotificationAsReadHandler
{
    public function __construct(
        private NotificationRepositoryInterface $notifications,
    ) {}

    public function handle(MarkNotificationAsReadCommand $command): void
    {
        $notification = $this->notifications->findByIdAndUser(
            id: $command->notificationId,
            userId: $command->userId,
        );

        if (null === $notification) {
            throw new NotificationNotFoundException($command->notificationId);
        }

        $this->notifications->markAsRead($command->notificationId);
    }
}
