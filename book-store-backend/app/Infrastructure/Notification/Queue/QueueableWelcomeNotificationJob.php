<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Queue;

use App\Application\Notification\Interfaces\NotificationSenderInterface;
use App\Application\Notification\Jobs\SendWelcomeNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class QueueableWelcomeNotificationJob implements ShouldQueue
{
    use Queueable;

    private const string QUEUE = 'notifications';

    public int    $tries  = 3;

    public int    $backoff = 30;

    public function __construct(
        private readonly int    $userId,
        private readonly string $userName,
        private readonly string $userEmail,
    ) {
        $this->onQueue(self::QUEUE);
    }

    public function handle(NotificationSenderInterface $sender): void
    {
        new SendWelcomeNotificationJob(
            userId: $this->userId,
            userName: $this->userName,
            userEmail: $this->userEmail,
            sender: $sender,
        )->handle();
    }
}
