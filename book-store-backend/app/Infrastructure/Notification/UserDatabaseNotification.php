<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Notification\ValueObjects\NotificationContent;
use Illuminate\Notifications\Notification;

final class UserDatabaseNotification extends Notification
{
    public function __construct(
        private readonly NotificationContent $content,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => $this->content->type->value,
            'title' => $this->content->title,
            'body'  => $this->content->body,
            'data'  => $this->content->data,
        ];
    }
}
