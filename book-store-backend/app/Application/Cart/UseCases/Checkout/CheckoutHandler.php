<?php

declare(strict_types=1);

namespace App\Application\Cart\UseCases\Checkout;

use App\Application\Cart\Interfaces\PaymentGatewayInterface;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Interfaces\CartRepositoryInterface;
use App\Domain\Shared\ValueObjects\Currency;

final readonly class CheckoutHandler
{
    public function __construct(
        private CartRepositoryInterface $carts,
        private PaymentGatewayInterface $paymentGateway,
    ) {}

    public function handle(CheckoutCommand $command): CheckoutResult
    {
        $cart = $this->carts->findActiveByUser($command->userId)
            ?? throw new CartNotFoundException($command->userId);

        $currency = new Currency($command->currency);
        $total    = $cart->total($currency);

        $checkedOut = $this->carts->save($cart->checkout());

        $paymentUrl = $this->paymentGateway->createSession(
            cartId: $checkedOut->id->value,
            amount: $total,
            metadata: ['user_id' => $command->userId],
        );

        return new CheckoutResult(
            cartId: $checkedOut->id->value,
            total: $total,
            paymentUrl: $paymentUrl,
        );
    }
}
