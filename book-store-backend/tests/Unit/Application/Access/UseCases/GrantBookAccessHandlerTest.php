<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Access\UseCases;

use App\Application\Access\UseCases\GrantBookAccess\GrantBookAccessCommand;
use App\Application\Access\UseCases\GrantBookAccess\GrantBookAccessHandler;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeUserBookAccessRepository;

final class GrantBookAccessHandlerTest extends TestCase
{
    private FakeUserBookAccessRepository $repo;

    private GrantBookAccessHandler       $handler;

    protected function setUp(): void
    {
        $this->repo    = new FakeUserBookAccessRepository();
        $this->handler = new GrantBookAccessHandler($this->repo);
    }

    public function test_grants_access_when_none_exists(): void
    {
        $this->handler->handle(new GrantBookAccessCommand(userId: 1, bookId: 10));

        $this->repo->assertHasAccess(1, 10);
    }

    public function test_stores_stripe_payment_intent_id(): void
    {
        $this->handler->handle(
            new GrantBookAccessCommand(userId: 1, bookId: 10, stripePaymentIntentId: 'pi_abc'),
        );

        $access = $this->repo->findByUserAndBook(1, 10);

        $this->assertSame('pi_abc', $access?->stripePaymentIntentId);
    }

    public function test_is_idempotent_when_access_already_granted(): void
    {
        $this->handler->handle(new GrantBookAccessCommand(userId: 1, bookId: 10));
        $this->handler->handle(new GrantBookAccessCommand(userId: 1, bookId: 10));

        $this->repo->assertCount(1);
    }

    public function test_grants_access_independently_per_book(): void
    {
        $this->handler->handle(new GrantBookAccessCommand(userId: 1, bookId: 10));
        $this->handler->handle(new GrantBookAccessCommand(userId: 1, bookId: 20));

        $this->repo->assertCount(2);
    }

    public function test_grants_access_independently_per_user(): void
    {
        $this->handler->handle(new GrantBookAccessCommand(userId: 1, bookId: 10));
        $this->handler->handle(new GrantBookAccessCommand(userId: 2, bookId: 10));

        $this->repo->assertCount(2);
    }
}
