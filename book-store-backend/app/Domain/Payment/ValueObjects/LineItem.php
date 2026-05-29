<?php

namespace App\Domain\Payment\ValueObjects;

use App\Domain\Shared\ValueObjects\Money;

final readonly class LineItem
{
    public function __construct(
        public string $name,
        public string $description,
        public Money  $price,
    ) {}
}
