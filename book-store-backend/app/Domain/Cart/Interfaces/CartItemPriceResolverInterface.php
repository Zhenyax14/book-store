<?php

declare(strict_types=1);

namespace App\Domain\Cart\Interfaces;

use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Shared\ValueObjects\Money;

interface CartItemPriceResolverInterface
{
    /**
     * @return array{title: string, price: Money}
     */
    public function resolve(CartItemTypeEnum $type, int $referenceId): array;
}
