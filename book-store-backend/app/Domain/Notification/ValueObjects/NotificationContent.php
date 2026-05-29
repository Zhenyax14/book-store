<?php

declare(strict_types=1);

namespace App\Domain\Notification\ValueObjects;

use App\Domain\Notification\Enums\NotificationTypeEnum;

final readonly class NotificationContent
{
    public function __construct(
        public NotificationTypeEnum $type,
        public string               $title,
        public string               $body,
        public array                $data = [],
    ) {}
}
