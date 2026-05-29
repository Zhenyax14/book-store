<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Application\Notification\DTOs\NotificationDispatchRequest;
use App\Application\Notification\Interfaces\NotificationSenderInterface;
use App\Domain\Notification\Enums\NotificationChannelEnum;
use App\Infrastructure\Persistence\Models\UserModel;

final readonly class DatabaseNotificationSender implements NotificationSenderInterface
{
    public function supports(NotificationChannelEnum $channel): bool
    {
        return NotificationChannelEnum::DATABASE === $channel;
    }

    public function send(NotificationDispatchRequest $request): void
    {
        $model = UserModel::query()->find($request->userId);

        $model?->notify(new UserDatabaseNotification($request->content));
    }
}
