<?php

declare(strict_types=1);

namespace App\Domain\Cart\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class CartNotFoundException extends \DomainException
{
    public function __construct(int $userId)
    {
        parent::__construct("Active cart for user={$userId} not found.", Response::HTTP_NOT_FOUND);
    }
}
