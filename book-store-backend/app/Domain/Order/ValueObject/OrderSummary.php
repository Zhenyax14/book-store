<?php

declare(strict_types=1);

namespace App\Domain\Order\ValueObject;

use App\Domain\Shared\ValueObjects\Money;
use DateTimeImmutable;

final readonly class OrderSummary
{
    /** @param OrderItem[] $items */
    public function __construct(
        public int               $cartId,
        public int               $userId,
        public string            $userEmail,
        public string            $userName,
        public array             $items,
        public Money             $total,
        public DateTimeImmutable $checkedOutAt,
        public ?string           $stripePaymentIntentId,
    ) {}

    public function itemCount(): int
    {
        return count($this->items);
    }
}
