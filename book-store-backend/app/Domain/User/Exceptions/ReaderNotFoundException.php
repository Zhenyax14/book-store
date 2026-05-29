<?php

declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

class ReaderNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Reader with id={$id} not found", Response::HTTP_NOT_FOUND);
    }
}
