<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Notification\Entities\UserNotification;
use App\Domain\Notification\Interfaces\NotificationRepositoryInterface;
use App\Domain\Notification\ValueObjects\NotificationCollection;
use PHPUnit\Framework\Assert;

final class FakeNotificationRepository implements NotificationRepositoryInterface
{
    /** @var array<string, UserNotification> */
    private array $store = [];

    /** @var array<string> */
    private array $markedAsRead = [];

    private bool $allMarkedAsRead = false;

    public function findByUser(int $userId, int $perPage, int $page): NotificationCollection
    {
        $all = array_values(array_filter(
            $this->store,
            static fn(UserNotification $n) => $n->userId === $userId,
        ));

        $unread = count(array_filter($all, static fn(UserNotification $n) => ! $n->isRead()));

        $offset = ($page - 1) * $perPage;
        $items  = array_slice($all, $offset, $perPage);

        return new NotificationCollection(
            items: $items,
            total: count($all),
            perPage: $perPage,
            currentPage: $page,
            unreadCount: $unread,
        );
    }

    public function findByIdAndUser(string $id, int $userId): ?UserNotification
    {
        $n = $this->store[$id] ?? null;

        return (null !== $n && $n->userId === $userId) ? $n : null;
    }

    public function markAsRead(string $id): void
    {
        $this->markedAsRead[] = $id;

        if (isset($this->store[$id])) {
            $n = $this->store[$id];
            $this->store[$id] = new UserNotification(
                id: $n->id,
                userId: $n->userId,
                content: $n->content,
                readAt: new \DateTimeImmutable(),
                createdAt: $n->createdAt,
            );
        }
    }

    public function markAllAsRead(int $userId): void
    {
        $this->allMarkedAsRead = true;

        foreach ($this->store as $id => $n) {
            if ($n->userId === $userId && ! $n->isRead()) {
                $this->store[$id] = new UserNotification(
                    id: $n->id,
                    userId: $n->userId,
                    content: $n->content,
                    readAt: new \DateTimeImmutable(),
                    createdAt: $n->createdAt,
                );
            }
        }
    }

    public function countUnread(int $userId): int
    {
        return count(array_filter(
            $this->store,
            static fn(UserNotification $n) => $n->userId === $userId && ! $n->isRead(),
        ));
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function add(UserNotification $notification): void
    {
        $this->store[$notification->id] = $notification;
    }

    // ── Assertions ────────────────────────────────────────────────

    public function assertMarkedAsRead(string $id): void
    {
        Assert::assertContains(
            $id,
            $this->markedAsRead,
            "Expected notification #{$id} to be marked as read.",
        );
    }

    public function assertNotMarkedAsRead(string $id): void
    {
        Assert::assertNotContains(
            $id,
            $this->markedAsRead,
            "Expected notification #{$id} NOT to be marked as read.",
        );
    }

    public function assertAllMarkedAsRead(): void
    {
        Assert::assertTrue(
            $this->allMarkedAsRead,
            'Expected markAllAsRead() to be called.',
        );
    }
}
