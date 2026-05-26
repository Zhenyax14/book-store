<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Application\Notification\DTOs\NotificationDispatchRequest;
use App\Application\Notification\Interfaces\NotificationSenderInterface;
use App\Domain\Notification\Enums\NotificationChannelEnum;
use Illuminate\Support\Facades\Mail;

final readonly class MailNotificationSender implements NotificationSenderInterface
{
    public function supports(NotificationChannelEnum $channel): bool
    {
        return NotificationChannelEnum::EMAIL === $channel;
    }

    public function send(NotificationDispatchRequest $request): void
    {
        Mail::to($request->userEmail)
            ->queue(NotificationMailFactory::make($request->content));
    }
}
