<?php

declare(strict_types=1);

namespace App\Domain\Notification\Entities;

use App\Domain\Notification\ValueObjects\NotificationContent;
use DateTimeImmutable;

final readonly class UserNotification
{
    public function __construct(
        public string               $id,
        public int                  $userId,
        public NotificationContent  $content,
        public ?DateTimeImmutable   $readAt,
        public DateTimeImmutable    $createdAt,
    ) {}

    public function isRead(): bool
    {
        return null !== $this->readAt;
    }
}
