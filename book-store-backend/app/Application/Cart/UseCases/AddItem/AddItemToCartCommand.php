<?php

declare(strict_types=1);

namespace App\Application\Cart\UseCases\AddItem;

use App\Domain\Cart\Enums\CartItemTypeEnum;

final readonly class AddItemToCartCommand
{
    public function __construct(
        public int              $userId,
        public CartItemTypeEnum $type,
        public int              $referenceId,
    ) {}
}
