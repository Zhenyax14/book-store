<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Notification\Entities\UserNotification;
use App\Domain\Notification\Exceptions\NotificationNotFoundException;
use App\Domain\Notification\Interfaces\NotificationRepositoryInterface;
use App\Domain\Notification\ValueObjects\NotificationCollection;
use App\Infrastructure\Persistence\Models\NotificationModel;
use DateMalformedStringException;
use DateTimeImmutable;
use App\Domain\Notification\Enums\NotificationTypeEnum;
use App\Domain\Notification\ValueObjects\NotificationContent;

final class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function findByUser(int $userId, int $perPage, int $page): NotificationCollection
    {
        $paginator = NotificationModel::query()
            ->forUser($userId)
            ->recentFirst()
            ->paginate(perPage: $perPage, page: $page);

        $unread = NotificationModel::query()
            ->forUser($userId)
            ->unread()
            ->count();

        return new NotificationCollection(
            items: array_map(
                /**
                 * @throws DateMalformedStringException
                 */
                fn(NotificationModel $model) => $this->toDomain($model),
                $paginator->items(),
            ),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            unreadCount: $unread,
        );
    }

    /**
     * @throws DateMalformedStringException
     */
    public function findByIdAndUser(string $id, int $userId): ?UserNotification
    {
        if ( ! $this->isValidUuid($id)) {
            return null;
        }

        $model = NotificationModel::query()
            ->forUser($userId)
            ->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function markAsRead(string $id): void
    {
        if ( ! $this->isValidUuid($id)) {
            throw new NotificationNotFoundException($id);
        }

        NotificationModel::query()
            ->findOrFail($id)
            ->markAsRead();
    }

    public function markAllAsRead(int $userId): void
    {
        NotificationModel::query()
            ->forUser($userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public function countUnread(int $userId): int
    {
        return NotificationModel::query()
            ->forUser($userId)
            ->unread()
            ->count();
    }

    /**
     * @throws DateMalformedStringException
     */
    private function toDomain(NotificationModel $model): UserNotification
    {
        return new UserNotification(
            id: $model->id,
            userId: $model->notifiable_id,
            content: new NotificationContent(
                type: NotificationTypeEnum::from($model->data['type']),
                title: $model->data['title'],
                body: $model->data['body'],
                data: $model->data['data'] ?? [],
            ),
            readAt: $model->read_at
                ? new DateTimeImmutable($model->read_at->toISOString())
                : null,
            createdAt: new DateTimeImmutable($model->created_at->toISOString()),
        );
    }

    private function isValidUuid(string $id): bool
    {
        return (bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $id,
        );
    }
}
