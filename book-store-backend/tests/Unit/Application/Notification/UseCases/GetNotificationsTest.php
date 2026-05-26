<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Notification\UseCases;

use App\Application\Notification\UseCases\GetNotifications\GetNotificationsCommand;
use App\Application\Notification\UseCases\GetNotifications\GetNotificationsHandler;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeNotificationRepository;
use Tests\Fakes\NotificationFactory;

final class GetNotificationsTest extends TestCase
{
    private FakeNotificationRepository $repo;

    private GetNotificationsHandler    $handler;

    protected function setUp(): void
    {
        $this->repo    = new FakeNotificationRepository();
        $this->handler = new GetNotificationsHandler($this->repo);
    }

    public function test_returns_empty_collection_when_no_notifications(): void
    {
        $result = $this->handler->handle(new GetNotificationsCommand(userId: 1));

        $this->assertCount(0, $result->collection->items);
        $this->assertSame(0, $result->collection->total);
        $this->assertSame(0, $result->collection->unreadCount);
    }

    public function test_returns_only_notifications_for_given_user(): void
    {
        $this->repo->add(NotificationFactory::make(userId: 1));
        $this->repo->add(NotificationFactory::make(userId: 1));
        $this->repo->add(NotificationFactory::make(userId: 2));

        $result = $this->handler->handle(new GetNotificationsCommand(userId: 1));

        $this->assertSame(2, $result->collection->total);
    }

    public function test_unread_count_reflects_unread_notifications(): void
    {
        $this->repo->add(NotificationFactory::make(userId: 1, isRead: false));
        $this->repo->add(NotificationFactory::make(userId: 1, isRead: false));
        $this->repo->add(NotificationFactory::make(userId: 1, isRead: true));

        $result = $this->handler->handle(new GetNotificationsCommand(userId: 1));

        $this->assertSame(2, $result->collection->unreadCount);
    }

    public function test_pagination_respects_per_page(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->repo->add(NotificationFactory::make(userId: 1));
        }

        $result = $this->handler->handle(
            new GetNotificationsCommand(userId: 1, perPage: 2, page: 1),
        );

        $this->assertCount(2, $result->collection->items);
        $this->assertSame(5, $result->collection->total);
        $this->assertSame(3, $result->collection->totalPages());
    }

    public function test_pagination_second_page(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->repo->add(NotificationFactory::make(userId: 1));
        }

        $result = $this->handler->handle(
            new GetNotificationsCommand(userId: 1, perPage: 2, page: 2),
        );

        $this->assertCount(2, $result->collection->items);
        $this->assertSame(2, $result->collection->currentPage);
    }
}
