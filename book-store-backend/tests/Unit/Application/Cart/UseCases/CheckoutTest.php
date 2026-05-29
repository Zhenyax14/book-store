<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\AddItem\AddItemToCartCommand;
use App\Application\Cart\UseCases\AddItem\AddItemToCartHandler;
use App\Application\Cart\UseCases\Checkout\CheckoutCommand;
use App\Application\Cart\UseCases\Checkout\CheckoutHandler;
use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Cart\Enums\CartStatusEnum;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeCartItemPriceResolver;
use Tests\Fakes\FakeCartRepository;
use Tests\Fakes\FakePaymentGateway;

final class CheckoutTest extends TestCase
{
    private FakeCartRepository $carts;

    private FakePaymentGateway $gateway;

    private CheckoutHandler $handler;

    protected function setUp(): void
    {
        $this->carts = new FakeCartRepository();
        $this->gateway = new FakePaymentGateway();
        $this->handler = new CheckoutHandler($this->carts, $this->gateway);

        $resolver = new FakeCartItemPriceResolver();
        $resolver->register(CartItemTypeEnum::BOOK, 1, 'Clean Code', Money::ofEur(1990));
        $resolver->register(CartItemTypeEnum::SUBSCRIPTION, 1, 'Monthly', Money::ofEur(990));

        $addHandler = new AddItemToCartHandler($this->carts, $resolver);
        $addHandler->handle(new AddItemToCartCommand(1, CartItemTypeEnum::BOOK, 1));
        $addHandler->handle(new AddItemToCartCommand(1, CartItemTypeEnum::SUBSCRIPTION, 1));
    }

    public function test_checkout_marks_cart_as_checked_out(): void
    {
        $result = $this->handler->handle(new CheckoutCommand(userId: 1, currency: 'EUR'));

        $this->carts->assertCartHasStatus($result->cartId, CartStatusEnum::CHECKED_OUT);
    }

    public function test_checkout_returns_correct_total(): void
    {
        $result = $this->handler->handle(new CheckoutCommand(userId: 1, currency: 'EUR'));

        $this->assertSame(2980, $result->total->amount); // 1990 + 990
    }

    public function test_checkout_returns_payment_url(): void
    {
        $result = $this->handler->handle(new CheckoutCommand(userId: 1, currency: 'EUR'));

        $this->assertStringStartsWith('https://fake-payment.test/session/', $result->paymentUrl);
    }

    public function test_checkout_calls_payment_gateway_with_correct_amount(): void
    {
        $result = $this->handler->handle(new CheckoutCommand(userId: 1, currency: 'EUR'));

        $this->gateway->assertSessionCreatedFor($result->cartId);
        $this->gateway->assertAmount(2980);
    }

    public function test_checkout_throws_when_no_active_cart(): void
    {
        $this->expectException(CartNotFoundException::class);

        $this->handler->handle(new CheckoutCommand(userId: 99, currency: 'EUR'));
    }
}
