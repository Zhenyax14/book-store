<?php

declare(strict_types=1);

namespace App\Domain\Cart\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class CartAlreadyCheckedOutException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Cart has already been checked out.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
