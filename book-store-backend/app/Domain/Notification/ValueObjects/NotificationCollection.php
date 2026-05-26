<?php

declare(strict_types=1);

namespace App\Domain\Notification\ValueObjects;

use App\Domain\Notification\Entities\UserNotification;

/**
 * @property UserNotification[] $items
 * @property int $total
 * @property int $perPage
 * @property int $currentPage
 * @property int $unreadCount
 */
final readonly class NotificationCollection
{
    public function __construct(
        /** @var UserNotification[] */
        public array $items,
        public int   $total,
        public int   $perPage,
        public int   $currentPage,
        public int   $unreadCount,
    ) {}

    public function totalPages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }
}
