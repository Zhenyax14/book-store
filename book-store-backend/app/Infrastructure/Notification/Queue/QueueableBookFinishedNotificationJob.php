<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Queue;

use App\Application\Notification\Interfaces\NotificationSenderInterface;
use App\Application\Notification\Jobs\SendBookFinishedNotificationJob;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class QueueableBookFinishedNotificationJob implements ShouldQueue
{
    use Queueable;

    private const string QUEUE = 'notifications';

    public int    $tries  = 3;

    public int    $backoff = 30;

    public function __construct(
        private readonly int    $userId,
        private readonly int    $bookId,
        private readonly string $bookTitle,
    ) {
        $this->onQueue(self::QUEUE);
    }

    public function handle(NotificationSenderInterface $sender): void
    {
        $user = UserModel::query()->find($this->userId);

        if (null === $user) {
            return;
        }

        new SendBookFinishedNotificationJob(
            userId: $this->userId,
            userEmail: $user->email,
            bookId: $this->bookId,
            bookTitle: $this->bookTitle,
            sender: $sender,
        )->handle();
    }
}
