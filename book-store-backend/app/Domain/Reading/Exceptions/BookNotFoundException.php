<?php

namespace App\Domain\Reading\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class BookNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Book with id={$id} not found", Response::HTTP_NOT_FOUND);
    }
}
