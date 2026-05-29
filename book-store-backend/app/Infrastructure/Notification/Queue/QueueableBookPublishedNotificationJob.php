<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Queue;

use App\Application\Notification\Interfaces\NotificationSenderInterface;
use App\Application\Notification\Jobs\SendBookPublishedNotificationJob;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class QueueableBookPublishedNotificationJob implements ShouldQueue
{
    use Queueable;

    private const string QUEUE = 'notifications';

    public int    $tries  = 3;

    public int    $backoff = 30;

    public function __construct(
        private readonly int    $bookId,
        private readonly string $bookTitle,
    ) {
        $this->onQueue(self::QUEUE);
    }

    public function handle(NotificationSenderInterface $sender): void
    {
        UserModel::query()
            ->select(['id', 'email'])
            ->chunkById(500, function (iterable $users) use ($sender): void {
                foreach ($users as $user) {
                    new SendBookPublishedNotificationJob(
                        userId: $user->id,
                        userEmail: $user->email,
                        bookId: $this->bookId,
                        bookTitle: $this->bookTitle,
                        sender: $sender,
                    )->handle();
                }
            });
    }
}
