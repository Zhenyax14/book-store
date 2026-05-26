<?php

declare(strict_types=1);

namespace App\Domain\Reading\ValueObjects;

use App\Domain\Reading\Entities\BookPage;

final readonly class AdjacentPages
{
    public function __construct(
        public ?BookPage $previous,
        public ?BookPage $next,
    ) {}

    public function hasPrevious(): bool
    {
        return null !== $this->previous;
    }

    public function hasNext(): bool
    {
        return null !== $this->next;
    }
}
