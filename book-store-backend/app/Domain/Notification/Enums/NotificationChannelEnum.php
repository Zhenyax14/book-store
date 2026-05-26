<?php

declare(strict_types=1);

namespace App\Domain\Notification\Enums;

enum NotificationChannelEnum: string
{
    case DATABASE = 'database';
    case EMAIL    = 'email';
}
