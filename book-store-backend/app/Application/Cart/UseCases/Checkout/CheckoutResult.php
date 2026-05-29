<?php

declare(strict_types=1);

namespace App\Application\Cart\UseCases\Checkout;

use App\Domain\Shared\ValueObjects\Money;

final readonly class CheckoutResult
{
    public function __construct(
        public int    $cartId,
        public Money  $total,
        public string $paymentUrl,
    ) {}
}
