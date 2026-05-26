<?php

declare(strict_types=1);

namespace App\Domain\Cart\Exceptions;

use App\Domain\Cart\Enums\CartItemTypeEnum;
use Symfony\Component\HttpFoundation\Response;

final class CartItemAlreadyExistsException extends \DomainException
{
    public function __construct(CartItemTypeEnum $type, int $referenceId)
    {
        parent::__construct(
            "Item [{$type->value}:{$referenceId}] is already in the cart.",
            Response::HTTP_CONFLICT,
        );
    }
}
