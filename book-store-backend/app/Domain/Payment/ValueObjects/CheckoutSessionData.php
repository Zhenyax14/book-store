<?php

declare(strict_types=1);

namespace App\Domain\Payment\ValueObjects;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final readonly class CheckoutSessionData
{
    /** @param LineItem[] $lineItems */
    public function __construct(
        public int    $cartId,
        public int    $userId,
        public array  $lineItems,
        public string $currency,
    ) {
        if (empty($this->lineItems)) {
            throw new InvalidArgumentException('Checkout session must have at least one line item.', Response::HTTP_BAD_REQUEST);
        }
    }
}
