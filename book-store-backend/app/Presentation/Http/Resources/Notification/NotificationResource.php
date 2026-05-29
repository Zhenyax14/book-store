<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Notification;

use App\Domain\Notification\Entities\UserNotification;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property UserNotification $resource */
final class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $n = $this->resource;

        return [
            'id'         => $n->id,
            'type'       => $n->content->type->value,
            'title'      => $n->content->title,
            'body'       => $n->content->body,
            'data'       => $n->content->data,
            'is_read'    => $n->isRead(),
            'read_at'    => $n->readAt?->format(DateTimeInterface::ATOM),
            'created_at' => $n->createdAt->format(DateTimeInterface::ATOM),
        ];
    }
}
