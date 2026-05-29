<?php

declare(strict_types=1);

namespace App\Domain\Cart\Exceptions;

use App\Domain\Cart\Enums\CartItemTypeEnum;
use Symfony\Component\HttpFoundation\Response;

final class CartItemNotFoundException extends \DomainException
{
    public function __construct(CartItemTypeEnum $type, int $referenceId)
    {
        parent::__construct(
            "Item [{$type->value}:{$referenceId}] not found in cart.",
            Response::HTTP_NOT_FOUND,
        );
    }
}
