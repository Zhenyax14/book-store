<?php

declare(strict_types=1);

namespace App\Domain\Cart\ValueObjects;

final readonly class CartItemId
{
    public function __construct(public int $value) {}
}
