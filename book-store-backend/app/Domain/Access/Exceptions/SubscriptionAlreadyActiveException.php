<?php

declare(strict_types=1);

namespace App\Domain\Access\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class SubscriptionAlreadyActiveException extends \DomainException
{
    public function __construct(int $userId)
    {
        parent::__construct(
            "User {$userId} already has an active subscription.",
            Response::HTTP_CONFLICT,
        );
    }
}
