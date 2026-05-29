<?php

namespace App\Domain\Reading\Exceptions;

use App\Domain\Reading\Enums\ReadingStatusEnum;
use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidReadingStatusTransitionException extends DomainException
{
    public function __construct(ReadingStatusEnum $from, ReadingStatusEnum $to)
    {
        parent::__construct(
            "Cannot transition reading status from [{$from->value}] to [{$to->value}]",
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
