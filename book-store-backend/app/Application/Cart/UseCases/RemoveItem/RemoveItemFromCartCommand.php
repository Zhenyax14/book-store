<?php

declare(strict_types=1);

namespace App\Application\Cart\UseCases\RemoveItem;

use App\Domain\Cart\Enums\CartItemTypeEnum;

final readonly class RemoveItemFromCartCommand
{
    public function __construct(
        public int              $userId,
        public CartItemTypeEnum $type,
        public int              $referenceId,
    ) {}
}
