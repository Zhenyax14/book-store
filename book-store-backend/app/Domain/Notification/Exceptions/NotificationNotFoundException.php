<?php

declare(strict_types=1);

namespace App\Domain\Notification\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class NotificationNotFoundException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct(
            "Notification with id={$id} not found",
            Response::HTTP_NOT_FOUND,
        );
    }
}
