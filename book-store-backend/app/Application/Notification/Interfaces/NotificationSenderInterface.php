<?php

declare(strict_types=1);

namespace App\Application\Notification\Interfaces;

use App\Application\Notification\DTOs\NotificationDispatchRequest;
use App\Domain\Notification\Enums\NotificationChannelEnum;

interface NotificationSenderInterface
{
    public function supports(NotificationChannelEnum $channel): bool;

    public function send(NotificationDispatchRequest $request): void;
}
