<?php

declare(strict_types=1);

namespace App\Domain\Order\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

class OrderNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Order with id={$id} not found", Response::HTTP_NOT_FOUND);
    }
}
