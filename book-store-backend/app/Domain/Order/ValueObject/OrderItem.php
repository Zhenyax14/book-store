<?php

declare(strict_types=1);

namespace App\Domain\Order\ValueObject;

use App\Domain\Order\Enums\OrderItemTypeEnum;
use App\Domain\Shared\ValueObjects\Money;

final readonly class OrderItem
{
    public function __construct(
        public OrderItemTypeEnum $type,
        public int              $referenceId,
        public string           $title,
        public Money            $price,
        public bool             $accessGranted,
    ) {}
}
