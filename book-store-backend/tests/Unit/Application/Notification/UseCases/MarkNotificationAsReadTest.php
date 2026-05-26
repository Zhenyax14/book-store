<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Notification\UseCases;

use App\Application\Notification\UseCases\MarkNotificationAsRead\MarkNotificationAsReadCommand;
use App\Application\Notification\UseCases\MarkNotificationAsRead\MarkNotificationAsReadHandler;
use App\Domain\Notification\Exceptions\NotificationNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeNotificationRepository;
use Tests\Fakes\NotificationFactory;

final class MarkNotificationAsReadTest extends TestCase
{
    private FakeNotificationRepository $repo;

    private MarkNotificationAsReadHandler $handler;

    protected function setUp(): void
    {
        $this->repo = new FakeNotificationRepository();
        $this->handler = new MarkNotificationAsReadHandler($this->repo);
    }

    public function test_marks_notification_as_read(): void
    {
        $notification = NotificationFactory::make(userId: 1, id: 'uuid-111', isRead: false);
        $this->repo->add($notification);

        $this->handler->handle(new MarkNotificationAsReadCommand(
            userId: 1,
            notificationId: 'uuid-111',
        ));

        $this->repo->assertMarkedAsRead('uuid-111');
    }

    public function test_throws_when_notification_not_found(): void
    {
        $this->expectException(NotificationNotFoundException::class);

        $this->handler->handle(new MarkNotificationAsReadCommand(
            userId: 1,
            notificationId: 'non-existent-uuid',
        ));
    }

    public function test_throws_when_notification_belongs_to_different_user(): void
    {
        $notification = NotificationFactory::make(userId: 2, id: 'uuid-222');
        $this->repo->add($notification);

        $this->expectException(NotificationNotFoundException::class);

        $this->handler->handle(new MarkNotificationAsReadCommand(
            userId: 1,
            notificationId: 'uuid-222',
        ));

        $this->repo->assertNotMarkedAsRead('uuid-222');
    }
}
