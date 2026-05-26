<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Notification\UseCases;

use App\Application\Notification\UseCases\MarkAllNotificationsAsRead\MarkAllNotificationsAsReadCommand;
use App\Application\Notification\UseCases\MarkAllNotificationsAsRead\MarkAllNotificationsAsReadHandler;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeNotificationRepository;
use Tests\Fakes\NotificationFactory;

final class MarkAllNotificationsAsReadTest extends TestCase
{
    private FakeNotificationRepository         $repo;

    private MarkAllNotificationsAsReadHandler  $handler;

    protected function setUp(): void
    {
        $this->repo    = new FakeNotificationRepository();
        $this->handler = new MarkAllNotificationsAsReadHandler($this->repo);
    }

    public function test_marks_all_notifications_as_read(): void
    {
        $this->repo->add(NotificationFactory::make(userId: 1, isRead: false));
        $this->repo->add(NotificationFactory::make(userId: 1, isRead: false));

        $this->handler->handle(new MarkAllNotificationsAsReadCommand(userId: 1));

        $this->repo->assertAllMarkedAsRead();
    }

    public function test_does_not_fail_when_no_notifications(): void
    {
        $this->handler->handle(new MarkAllNotificationsAsReadCommand(userId: 1));

        $this->repo->assertAllMarkedAsRead();
    }
}
