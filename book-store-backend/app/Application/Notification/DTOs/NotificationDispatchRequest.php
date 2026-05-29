<?php

declare(strict_types=1);

namespace App\Application\Notification\DTOs;

use App\Domain\Notification\Enums\NotificationChannelEnum;
use App\Domain\Notification\ValueObjects\NotificationContent;

final readonly class NotificationDispatchRequest
{
    /** @param NotificationChannelEnum[] $channels */
    public function __construct(
        public int                  $userId,
        public string               $userEmail,
        public NotificationContent  $content,
        public array                $channels,
    ) {}
}
