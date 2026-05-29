<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Notification\Entities\UserNotification;
use App\Domain\Notification\Enums\NotificationTypeEnum;
use App\Domain\Notification\ValueObjects\NotificationContent;

final class NotificationFactory
{
    public static function make(
        int     $userId   = 1,
        ?string  $id       = null,
        bool    $isRead   = false,
        NotificationTypeEnum $type = NotificationTypeEnum::WELCOME,
    ): UserNotification {
        return new UserNotification(
            id: $id ?? \Ramsey\Uuid\Uuid::uuid4()->toString(),
            userId: $userId,
            content: new NotificationContent(
                type: $type,
                title: 'Test notification',
                body: 'Test body',
            ),
            readAt: $isRead ? new \DateTimeImmutable() : null,
            createdAt: new \DateTimeImmutable(),
        );
    }
}
