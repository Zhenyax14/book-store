<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Cart\Entities\Cart;
use App\Domain\Cart\Enums\CartStatusEnum;
use App\Domain\Cart\Interfaces\CartRepositoryInterface;
use App\Domain\Cart\ValueObjects\CartId;
use PHPUnit\Framework\Assert;

final class FakeCartRepository implements CartRepositoryInterface
{
    /** @var array<int, Cart> */
    private array $store  = [];

    private int   $nextId = 1;

    public function findActiveByUser(int $userId): ?Cart
    {
        return array_find($this->store, fn($cart) => $cart->userId === $userId && $cart->isActive());
    }

    public function findById(int $cartId): ?Cart
    {
        return $this->store[$cartId] ?? null;
    }

    public function save(Cart $cart): Cart
    {
        $id   = $cart->id?->value ?? $this->nextId++;
        $saved = new Cart(
            id: new CartId($id),
            userId: $cart->userId,
            status: $cart->status,
            items: $cart->items,
            createdAt: $cart->createdAt,
            checkedOutAt: $cart->checkedOutAt,
        );

        $this->store[$id] = $saved;

        return $saved;
    }

    public function assertCartSavedForUser(int $userId): void
    {
        $found = array_filter(
            $this->store,
            static fn(Cart $c) => $c->userId === $userId,
        );

        Assert::assertNotEmpty($found, "Expected cart for user={$userId} to be saved.");
    }

    public function assertCartHasStatus(int $cartId, CartStatusEnum $status): void
    {
        $cart = $this->store[$cartId] ?? null;
        Assert::assertNotNull($cart, "Cart #{$cartId} not found.");
        Assert::assertSame($status, $cart->status);
    }

    public function assertCount(int $expected): void
    {
        Assert::assertCount($expected, $this->store);
    }
}
