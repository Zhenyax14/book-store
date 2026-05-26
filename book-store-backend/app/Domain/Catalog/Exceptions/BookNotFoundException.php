<?php

namespace App\Domain\Catalog\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class BookNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Book with id={$id} not found", Response::HTTP_NOT_FOUND);
    }
}
