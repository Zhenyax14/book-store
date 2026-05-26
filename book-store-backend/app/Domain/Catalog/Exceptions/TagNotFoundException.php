<?php

namespace App\Domain\Catalog\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class TagNotFoundException extends DomainException
{
    public function __construct(array $ids)
    {
        parent::__construct('Tags with ids=' . implode(', ', $ids) . ' not found', Response::HTTP_NOT_FOUND);
    }
}
