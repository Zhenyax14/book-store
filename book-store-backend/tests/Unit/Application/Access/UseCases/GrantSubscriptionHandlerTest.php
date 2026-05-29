<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Access\UseCases;

use App\Application\Access\UseCases\GrantSubscription\GrantSubscriptionCommand;
use App\Application\Access\UseCases\GrantSubscription\GrantSubscriptionHandler;
use App\Domain\Shared\Enums\SubscriptionStatusEnum;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeUserSubscriptionRepository;

final class GrantSubscriptionHandlerTest extends TestCase
{
    private FakeUserSubscriptionRepository $repo;

    private GrantSubscriptionHandler       $handler;

    protected function setUp(): void
    {
        $this->repo    = new FakeUserSubscriptionRepository();
        $this->handler = new GrantSubscriptionHandler($this->repo);
    }

    public function test_creates_subscription_when_none_exists(): void
    {
        $expiresAt = new \DateTimeImmutable('+30 days');

        $this->handler->handle(new GrantSubscriptionCommand(
            userId: 1,
            stripeSubscriptionId: 'sub_xyz',
            expiresAt: $expiresAt,
        ));

        $sub = $this->repo->findActiveByUser(1);

        $this->assertNotNull($sub);
        $this->assertSame(SubscriptionStatusEnum::ACTIVE, $sub->status);
        $this->assertSame('sub_xyz', $sub->stripeSubscriptionId);
        $this->assertEquals($expiresAt, $sub->expiresAt);
    }

    public function test_is_idempotent_when_active_subscription_exists(): void
    {
        $command = new GrantSubscriptionCommand(
            userId: 1,
            stripeSubscriptionId: 'sub_idem',
            expiresAt: new \DateTimeImmutable('+30 days'),
        );

        $this->handler->handle($command);
        $this->handler->handle($command);

        $this->repo->assertCount(1);
    }

    public function test_subscription_has_active_status(): void
    {
        $this->handler->handle(new GrantSubscriptionCommand(
            userId: 1,
            stripeSubscriptionId: 'sub_active',
            expiresAt: new \DateTimeImmutable('+1 day'),
        ));

        $this->assertTrue($this->repo->findActiveByUser(1)?->isActive());
    }
}
