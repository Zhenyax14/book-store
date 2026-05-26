<?php

declare(strict_types=1);

namespace App\Domain\Notification\Interfaces;

use App\Domain\Notification\Entities\UserNotification;
use App\Domain\Notification\ValueObjects\NotificationCollection;

interface NotificationRepositoryInterface
{
    public function findByUser(int $userId, int $perPage, int $page): NotificationCollection;

    public function findByIdAndUser(string $id, int $userId): ?UserNotification;

    public function markAsRead(string $id): void;

    public function markAllAsRead(int $userId): void;

    public function countUnread(int $userId): int;
}
