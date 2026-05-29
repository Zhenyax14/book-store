<?php

namespace App\Domain\Subscription\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class SubscriptionNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Subscription with id={$id} not found", Response::HTTP_NOT_FOUND);
    }
}
