<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\AddItem\AddItemToCartCommand;
use App\Application\Cart\UseCases\AddItem\AddItemToCartHandler;
use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Cart\Exceptions\CartItemAlreadyExistsException;
use App\Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeCartItemPriceResolver;
use Tests\Fakes\FakeCartRepository;

final class AddItemToCartTest extends TestCase
{
    private FakeCartRepository        $carts;

    private AddItemToCartHandler      $handler;

    protected function setUp(): void
    {
        $this->carts    = new FakeCartRepository();
        $resolver = new FakeCartItemPriceResolver();
        $this->handler  = new AddItemToCartHandler($this->carts, $resolver);

        $resolver->register(CartItemTypeEnum::BOOK, 1, 'Clean Code', Money::ofEur(1990));
        $resolver->register(CartItemTypeEnum::BOOK, 2, 'DDD Book', Money::ofEur(2490));
        $resolver->register(CartItemTypeEnum::SUBSCRIPTION, 1, 'Monthly', Money::ofEur(990));
    }

    public function test_creates_new_cart_when_none_exists(): void
    {
        $cart = $this->handler->handle(
            new AddItemToCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );

        $this->assertNotNull($cart->id);
        $this->assertCount(1, $cart->items);
        $this->carts->assertCartSavedForUser(1);
    }

    public function test_adds_to_existing_cart(): void
    {
        $this->handler->handle(
            new AddItemToCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );

        $cart = $this->handler->handle(
            new AddItemToCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 2),
        );

        $this->assertCount(2, $cart->items);
        $this->carts->assertCount(1); // одна корзина на пользователя
    }

    public function test_item_receives_price_from_resolver(): void
    {
        $cart = $this->handler->handle(
            new AddItemToCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );

        $item = $cart->items[0];

        $this->assertSame(1990, $item->price->amount);
        $this->assertSame('Clean Code', $item->title);
    }

    public function test_can_add_book_and_subscription(): void
    {
        $this->handler->handle(
            new AddItemToCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );

        $cart = $this->handler->handle(
            new AddItemToCartCommand(userId: 1, type: CartItemTypeEnum::SUBSCRIPTION, referenceId: 1),
        );

        $this->assertCount(2, $cart->items);
    }

    public function test_throws_when_item_already_in_cart(): void
    {
        $this->handler->handle(
            new AddItemToCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );

        $this->expectException(CartItemAlreadyExistsException::class);

        $this->handler->handle(
            new AddItemToCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );
    }

    public function test_different_users_have_separate_carts(): void
    {
        $this->handler->handle(
            new AddItemToCartCommand(userId: 1, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );
        $this->handler->handle(
            new AddItemToCartCommand(userId: 2, type: CartItemTypeEnum::BOOK, referenceId: 1),
        );

        $this->carts->assertCount(2);
    }
}
