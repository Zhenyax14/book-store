<?php

declare(strict_types=1);

namespace App\Domain\Identity\Exceptions;

final class ReaderAlreadyExistsException extends \DomainException
{
    public static function withEmail(string $email): self
    {
        return new self("Reader with email '{$email}' already exists.");
    }
}
