<?php

namespace App\Domain\Reading\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class ReadingEntryNotFoundException extends DomainException
{
    public function __construct(int $userId, int $bookId)
    {
        parent::__construct(
            "Reading entry for user={$userId} book={$bookId} not found",
            Response::HTTP_NOT_FOUND,
        );
    }
}
