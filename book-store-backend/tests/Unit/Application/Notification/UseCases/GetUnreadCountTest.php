<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Notification\UseCases;

use App\Application\Notification\UseCases\GetUnreadCount\GetUnreadCountCommand;
use App\Application\Notification\UseCases\GetUnreadCount\GetUnreadCountHandler;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeNotificationRepository;
use Tests\Fakes\NotificationFactory;

final class GetUnreadCountTest extends TestCase
{
    private FakeNotificationRepository $repo;

    private GetUnreadCountHandler      $handler;

    protected function setUp(): void
    {
        $this->repo    = new FakeNotificationRepository();
        $this->handler = new GetUnreadCountHandler($this->repo);
    }

    public function test_returns_zero_when_no_notifications(): void
    {
        $count = $this->handler->handle(new GetUnreadCountCommand(userId: 1));

        $this->assertSame(0, $count);
    }

    public function test_counts_only_unread(): void
    {
        $this->repo->add(NotificationFactory::make(userId: 1, isRead: false));
        $this->repo->add(NotificationFactory::make(userId: 1, isRead: false));
        $this->repo->add(NotificationFactory::make(userId: 1, isRead: true));

        $count = $this->handler->handle(new GetUnreadCountCommand(userId: 1));

        $this->assertSame(2, $count);
    }

    public function test_counts_only_for_given_user(): void
    {
        $this->repo->add(NotificationFactory::make(userId: 1, isRead: false));
        $this->repo->add(NotificationFactory::make(userId: 2, isRead: false));

        $count = $this->handler->handle(new GetUnreadCountCommand(userId: 1));

        $this->assertSame(1, $count);
    }
}
