<?php

namespace App\Domain\Reading\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class ReadingEntryAlreadyExistsException extends DomainException
{
    public function __construct(int $bookId)
    {
        parent::__construct(
            "Book with id={$bookId} is already in the reading list",
            Response::HTTP_CONFLICT,
        );
    }
}
