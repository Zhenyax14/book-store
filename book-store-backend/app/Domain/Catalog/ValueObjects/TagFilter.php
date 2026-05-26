<?php

declare(strict_types=1);

namespace App\Domain\Catalog\ValueObjects;

final readonly class TagFilter
{
    public function __construct(
        public int             $perPage = 20,
        public int             $page = 1,
    ) {}
}
