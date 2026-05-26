<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Notification\UseCases\GetNotifications\GetNotificationsCommand;
use App\Application\Notification\UseCases\GetNotifications\GetNotificationsHandler;
use App\Application\Notification\UseCases\GetUnreadCount\GetUnreadCountCommand;
use App\Application\Notification\UseCases\GetUnreadCount\GetUnreadCountHandler;
use App\Application\Notification\UseCases\MarkAllNotificationsAsRead\MarkAllNotificationsAsReadCommand;
use App\Application\Notification\UseCases\MarkAllNotificationsAsRead\MarkAllNotificationsAsReadHandler;
use App\Application\Notification\UseCases\MarkNotificationAsRead\MarkNotificationAsReadCommand;
use App\Application\Notification\UseCases\MarkNotificationAsRead\MarkNotificationAsReadHandler;
use App\Presentation\Http\Requests\Notification\ListNotificationsRequest;
use App\Presentation\Http\Resources\Notification\NotificationCollectionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NotificationController extends Controller
{
    public function __construct(
        private readonly GetNotificationsHandler          $getHandler,
        private readonly GetUnreadCountHandler            $unreadCountHandler,
        private readonly MarkNotificationAsReadHandler    $markAsReadHandler,
        private readonly MarkAllNotificationsAsReadHandler $markAllAsReadHandler,
    ) {}

    /**
     * @response array{
     *     data: array<int, array{
     *         id: string,
     *         type: App\Domain\Notification\Enums\NotificationTypeEnum,
     *         title: string,
     *         body: string,
     *         data: array<string, mixed>,
     *         is_read: bool,
     *         read_at: string|null,
     *         created_at: string
     *     }>,
     *     meta: array{
     *         total: int,
     *         per_page: int,
     *         current_page: int,
     *         total_pages: int,
     *         unread_count: int
     *     }
     * }
     */
    public function index(ListNotificationsRequest $request): JsonResponse
    {
        $result = $this->getHandler->handle(
            GetNotificationsCommand::fromArray([
                'user_id' => $request->user()->id,
                ...$request->validated(),
            ]),
        );

        return new JsonResponse(
            new NotificationCollectionResource($result->collection),
        );
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->unreadCountHandler->handle(
            new GetUnreadCountCommand(userId: $request->user()->id),
        );

        return new JsonResponse(['count' => $count]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $this->markAsReadHandler->handle(
            new MarkNotificationAsReadCommand(
                userId: $request->user()->id,
                notificationId: $id,
            ),
        );

        return new JsonResponse(null, 204);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $this->markAllAsReadHandler->handle(
            new MarkAllNotificationsAsReadCommand(userId: $request->user()->id),
        );

        return new JsonResponse(null, 204);
    }
}
