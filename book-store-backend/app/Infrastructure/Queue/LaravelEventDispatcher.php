<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue;

use App\Application\Catalog\Events\BookFileUploaded;
use App\Application\Catalog\Jobs\ParseBookFileJob;
use App\Application\Shared\Interfaces\EventDispatcherInterface;
use App\Domain\Catalog\Events\BookPublished;
use App\Domain\Identity\Events\UserRegistered;
use App\Domain\Order\Events\PurchaseCompleted;
use App\Domain\Reading\Events\BookReadingFinished;
use App\Infrastructure\Notification\Queue\QueueableBookFinishedNotificationJob;
use App\Infrastructure\Notification\Queue\QueueableBookPublishedNotificationJob;
use App\Infrastructure\Notification\Queue\QueueablePurchaseReceiptNotificationJob;
use App\Infrastructure\Notification\Queue\QueueableWelcomeNotificationJob;

final class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        match (true) {
            $event instanceof BookFileUploaded => dispatch(
                new ParseBookFileJob(
                    bookId: $event->bookId,
                    filePath: $event->filePath,
                    mimeType: $event->mimeType,
                ),
            ),
            $event instanceof UserRegistered => dispatch(
                new QueueableWelcomeNotificationJob(
                    userId: $event->userId,
                    userName: $event->userName,
                    userEmail: $event->userEmail,
                ),
            ),
            $event instanceof BookReadingFinished => dispatch(
                new QueueableBookFinishedNotificationJob(
                    userId: $event->userId,
                    bookId: $event->bookId,
                    bookTitle: $event->bookTitle,
                ),
            ),
            $event instanceof BookPublished => dispatch(
                new QueueableBookPublishedNotificationJob(
                    bookId: $event->bookId,
                    bookTitle: $event->bookTitle,
                ),
            ),
            $event instanceof PurchaseCompleted => dispatch(
                new QueueablePurchaseReceiptNotificationJob(
                    userId: $event->userId,
                    bookId: $event->bookId,
                    bookTitle: $event->bookTitle,
                ),
            ),
            default => event($event),
        };
    }
}
