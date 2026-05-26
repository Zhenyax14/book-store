<?php

declare(strict_types=1);

namespace App\Application\Notification\UseCases\GetUnreadCount;

use App\Domain\Notification\Interfaces\NotificationRepositoryInterface;

final readonly class GetUnreadCountHandler
{
    public function __construct(
        private NotificationRepositoryInterface $notifications,
    ) {}

    public function handle(GetUnreadCountCommand $command): int
    {
        return $this->notifications->countUnread($command->userId);
    }
}
