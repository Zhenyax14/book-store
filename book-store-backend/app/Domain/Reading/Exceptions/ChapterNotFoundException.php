<?php

namespace App\Domain\Reading\Exceptions;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class ChapterNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Chapter with id={$id} not found", Response::HTTP_NOT_FOUND);
    }
}
