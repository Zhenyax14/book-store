<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Notification;

use App\Domain\Notification\Entities\UserNotification;
use App\Domain\Notification\ValueObjects\NotificationCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property NotificationCollection $resource
 * @property NotificationResource[] $data
 * @property int $total
 * @property int $perPage
 * @property int $currentPage
 * @property int $totalPages
 */
final class NotificationCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $collection = $this->resource;

        return [
            'data' => array_map(
                static fn(UserNotification $n) => new NotificationResource($n)->toArray($request),
                $collection->items,
            ),
            'meta' => [
                'total'        => $collection->total,
                'per_page'     => $collection->perPage,
                'current_page' => $collection->currentPage,
                'total_pages'  => $collection->totalPages(),
                'unread_count' => $collection->unreadCount,
            ],
        ];
    }
}
