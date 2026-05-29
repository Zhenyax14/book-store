<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Application\Notification\DTOs\NotificationDispatchRequest;
use App\Application\Notification\Interfaces\NotificationSenderInterface;
use App\Domain\Notification\Enums\NotificationChannelEnum;

final readonly class CompositeNotificationSender implements NotificationSenderInterface
{
    /** @param NotificationSenderInterface[] $senders */
    public function __construct(
        private array $senders,
    ) {}

    public function supports(NotificationChannelEnum $channel): bool
    {
        return true;
    }

    public function send(NotificationDispatchRequest $request): void
    {
        foreach ($request->channels as $channel) {
            foreach ($this->senders as $sender) {
                if ($sender->supports($channel)) {
                    $sender->send(new NotificationDispatchRequest(
                        userId: $request->userId,
                        userEmail: $request->userEmail,
                        content: $request->content,
                        channels: [$channel],
                    ));
                }
            }
        }
    }
}
