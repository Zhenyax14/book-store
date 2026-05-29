<?php

declare(strict_types=1);

namespace App\Application\Notification\UseCases\GetNotifications;

use App\Domain\Notification\Interfaces\NotificationRepositoryInterface;

final readonly class GetNotificationsHandler
{
    public function __construct(
        private NotificationRepositoryInterface $notifications,
    ) {}

    public function handle(GetNotificationsCommand $command): GetNotificationsResult
    {
        return new GetNotificationsResult(
            collection: $this->notifications->findByUser(
                userId: $command->userId,
                perPage: $command->perPage,
                page: $command->page,
            ),
        );
    }
}
