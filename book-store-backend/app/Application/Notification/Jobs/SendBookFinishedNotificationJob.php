<?php

declare(strict_types=1);

namespace App\Application\Notification\Jobs;

use App\Application\Notification\DTOs\NotificationDispatchRequest;
use App\Application\Notification\Interfaces\NotificationJobInterface;
use App\Application\Notification\Interfaces\NotificationSenderInterface;
use App\Domain\Notification\Enums\NotificationChannelEnum;
use App\Domain\Notification\Enums\NotificationTypeEnum;
use App\Domain\Notification\ValueObjects\NotificationContent;

final readonly class SendBookFinishedNotificationJob implements NotificationJobInterface
{
    public function __construct(
        private int                         $userId,
        private string                      $userEmail,
        private int                         $bookId,
        private string                      $bookTitle,
        private NotificationSenderInterface $sender,
    ) {}

    public function handle(): void
    {
        $this->sender->send(new NotificationDispatchRequest(
            userId: $this->userId,
            userEmail: $this->userEmail,
            content: new NotificationContent(
                type: NotificationTypeEnum::BOOK_FINISHED,
                title: 'Book Completed!',
                body: "You have finished \"{$this->bookTitle}\". Great job!",
                data: ['book_id' => $this->bookId],
            ),
            channels: [
                NotificationChannelEnum::DATABASE,
                NotificationChannelEnum::EMAIL,
            ],
        ));
    }
}
