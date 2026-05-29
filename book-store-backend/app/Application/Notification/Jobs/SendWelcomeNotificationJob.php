<?php

declare(strict_types=1);

namespace App\Application\Notification\Jobs;

use App\Application\Notification\DTOs\NotificationDispatchRequest;
use App\Application\Notification\Interfaces\NotificationJobInterface;
use App\Application\Notification\Interfaces\NotificationSenderInterface;
use App\Domain\Notification\Enums\NotificationChannelEnum;
use App\Domain\Notification\Enums\NotificationTypeEnum;
use App\Domain\Notification\ValueObjects\NotificationContent;

final readonly class SendWelcomeNotificationJob implements NotificationJobInterface
{
    public function __construct(
        private int                         $userId,
        private string                      $userName,
        private string                      $userEmail,
        private NotificationSenderInterface $sender,
    ) {}

    public function handle(): void
    {
        $this->sender->send(new NotificationDispatchRequest(
            userId: $this->userId,
            userEmail: $this->userEmail,
            content: new NotificationContent(
                type: NotificationTypeEnum::WELCOME,
                title: 'Welcome!',
                body: "Hello, {$this->userName}! We're glad to see you in our bookstore.",
            ),
            channels: [NotificationChannelEnum::EMAIL],
        ));
    }
}
