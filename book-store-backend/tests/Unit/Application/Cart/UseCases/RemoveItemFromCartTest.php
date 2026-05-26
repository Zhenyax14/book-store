<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\AddItem\AddItemToCartCommand;
use App\Application\Cart\UseCases\AddItem\AddItemToCartHandler;
use App\Application\Cart\UseCases\RemoveItem\RemoveItemFromCartCommand;
use App\Application\Cart\UseCases\RemoveItem\RemoveItemFromCartHandler;
use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeCartItemPriceResolver;
use Tests\Fakes\FakeCartRepository;

final class RemoveItemFromCartTest extends TestCase
{
    private RemoveItemFromCartHandler $handler;

    protected function setUp(): void
    {
        $carts = new FakeCartRepository();
        $resolver = new FakeCartItemPriceResolver();
        $this->handler  = new RemoveItemFromCartHandler($carts);

        $resolver->register(CartItemTypeEnum::BOOK, 1, 'Clean Code', Money::ofEur(1990));
        $resolver->register(CartItemTypeEnum::BOOK, 2, 'DDD Book', Money::ofEur(2490));

        $addHandler = new AddItemToCartHandler($carts, $resolver);
        $addHandler->handle(new AddItemToCartCommand(1, CartItemTypeEnum::BOOK, 1));
        $addHandler->handle(new AddItemToCartCommand(1, CartItemTypeEnum::BOOK, 2));
    }

    public function test_removes_item_from_cart(): void
    {
        $cart = $this->handler->handle(
            new RemoveItemFromCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );

        $this->assertCount(1, $cart->items);
        $this->assertSame(2, $cart->items[0]->referenceId);
    }

    public function test_throws_when_no_active_cart(): void
    {
        $this->expectException(CartNotFoundException::class);

        $this->handler->handle(
            new RemoveItemFromCartCommand(userId: 99, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );
    }

    public function test_throws_when_item_not_in_cart(): void
    {
        $this->expectException(CartItemNotFoundException::class);

        $this->handler->handle(
            new RemoveItemFromCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 99),
        );
    }
}
