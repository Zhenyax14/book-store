<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Cart\Entities;

use App\Domain\Cart\Entities\Cart;
use App\Domain\Cart\Entities\CartItem;
use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Cart\Enums\CartStatusEnum;
use App\Domain\Cart\Exceptions\CartAlreadyCheckedOutException;
use App\Domain\Cart\Exceptions\CartItemAlreadyExistsException;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

final class CartTest extends TestCase
{
    public function test_create_initializes_empty_active_cart(): void
    {
        $cart = Cart::create(userId: 1);

        $this->assertSame(1, $cart->userId);
        $this->assertSame(CartStatusEnum::ACTIVE, $cart->status);
        $this->assertEmpty($cart->items);
        $this->assertNull($cart->id);
        $this->assertNull($cart->checkedOutAt);
    }

    public function test_add_item_returns_new_cart_with_item(): void
    {
        $cart    = $this->makeCart();
        $item    = $this->makeBookItem();
        $updated = $cart->addItem($item);

        $this->assertCount(1, $updated->items);
        $this->assertCount(0, $cart->items);
    }

    public function test_add_multiple_different_items(): void
    {
        $cart = $this->makeCart()
            ->addItem($this->makeBookItem(bookId: 1))
            ->addItem($this->makeBookItem(bookId: 2))
            ->addItem($this->makeSubscriptionItem(planId: 1));

        $this->assertCount(3, $cart->items);
    }

    public function test_add_duplicate_item_throws(): void
    {
        $cart = $this->makeCart()->addItem($this->makeBookItem(bookId: 1));

        $this->expectException(CartItemAlreadyExistsException::class);

        $cart->addItem($this->makeBookItem(bookId: 1));
    }

    public function test_same_reference_id_different_types_are_allowed(): void
    {
        $cart = $this->makeCart()
            ->addItem($this->makeBookItem(bookId: 1))
            ->addItem($this->makeSubscriptionItem(planId: 1));

        $this->assertCount(2, $cart->items);
    }

    public function test_add_item_to_checked_out_cart_throws(): void
    {
        $cart = $this->makeCart()
            ->addItem($this->makeBookItem())
            ->checkout();

        $this->expectException(CartAlreadyCheckedOutException::class);

        $cart->addItem($this->makeBookItem(bookId: 2));
    }

    public function test_remove_item_returns_new_cart_without_item(): void
    {
        $cart    = $this->makeCart()->addItem($this->makeBookItem(bookId: 1));
        $updated = $cart->removeItem(CartItemTypeEnum::BOOK, referenceId: 1);

        $this->assertCount(0, $updated->items);
        $this->assertCount(1, $cart->items);
    }

    public function test_remove_item_leaves_other_items_intact(): void
    {
        $cart = $this->makeCart()
            ->addItem($this->makeBookItem(bookId: 1))
            ->addItem($this->makeBookItem(bookId: 2))
            ->removeItem(CartItemTypeEnum::BOOK, referenceId: 1);

        $this->assertCount(1, $cart->items);
        $this->assertSame(2, $cart->items[0]->referenceId);
    }

    public function test_remove_nonexistent_item_throws(): void
    {
        $cart = $this->makeCart();

        $this->expectException(CartItemNotFoundException::class);

        $cart->removeItem(CartItemTypeEnum::BOOK, referenceId: 99);
    }

    public function test_remove_item_from_checked_out_cart_throws(): void
    {
        $cart = $this->makeCart()
            ->addItem($this->makeBookItem())
            ->checkout();

        $this->expectException(CartAlreadyCheckedOutException::class);

        $cart->removeItem(CartItemTypeEnum::BOOK, referenceId: 1);
    }

    public function test_checkout_changes_status(): void
    {
        $cart = $this->makeCart()
            ->addItem($this->makeBookItem())
            ->checkout();

        $this->assertSame(CartStatusEnum::CHECKED_OUT, $cart->status);
        $this->assertNotNull($cart->checkedOutAt);
    }

    public function test_checkout_is_immutable(): void
    {
        $cart     = $this->makeCart()->addItem($this->makeBookItem());
        $checked  = $cart->checkout();

        $this->assertSame(CartStatusEnum::ACTIVE, $cart->status);
        $this->assertNotSame($cart, $checked);
    }

    public function test_checkout_empty_cart_throws(): void
    {
        $this->expectException(\DomainException::class);

        $this->makeCart()->checkout();
    }

    public function test_double_checkout_throws(): void
    {
        $cart = $this->makeCart()->addItem($this->makeBookItem())->checkout();

        $this->expectException(CartAlreadyCheckedOutException::class);

        $cart->checkout();
    }

    public function test_total_sums_all_items(): void
    {
        $cart = $this->makeCart()
            ->addItem($this->makeBookItem(priceAmount: 1990))
            ->addItem($this->makeSubscriptionItem(priceAmount: 990));

        $total = $cart->total(new Currency('EUR'));

        $this->assertSame(2980, $total->amount);
    }

    public function test_total_of_empty_cart_is_zero(): void
    {
        $total = $this->makeCart()->total(new Currency('EUR'));

        $this->assertTrue($total->isZero());
    }

    public function test_is_empty_returns_true_for_new_cart(): void
    {
        $this->assertTrue($this->makeCart()->isEmpty());
    }

    public function test_is_empty_returns_false_after_add(): void
    {
        $cart = $this->makeCart()->addItem($this->makeBookItem());

        $this->assertFalse($cart->isEmpty());
    }

    public function test_contains_item_returns_true_when_present(): void
    {
        $cart = $this->makeCart()->addItem($this->makeBookItem(bookId: 5));

        $this->assertTrue($cart->containsItem(CartItemTypeEnum::BOOK, 5));
    }

    public function test_contains_item_returns_false_when_absent(): void
    {
        $cart = $this->makeCart();

        $this->assertFalse($cart->containsItem(CartItemTypeEnum::BOOK, 5));
    }

    private function makeCart(): Cart
    {
        return Cart::create(userId: 1);
    }

    private function makeBookItem(int $bookId = 1, int $priceAmount = 1990): CartItem
    {
        return new CartItem(
            id: null,
            cartId: 0,
            type: CartItemTypeEnum::BOOK,
            referenceId: $bookId,
            title: "Book #{$bookId}",
            price: Money::ofEur($priceAmount),
        );
    }

    private function makeSubscriptionItem(int $planId = 1, int $priceAmount = 990): CartItem
    {
        return new CartItem(
            id: null,
            cartId: 0,
            type: CartItemTypeEnum::SUBSCRIPTION,
            referenceId: $planId,
            title: "Plan #{$planId}",
            price: Money::ofEur($priceAmount),
        );
    }
}
