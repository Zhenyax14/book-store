<?php

declare(strict_types=1);

namespace App\Domain\Cart\ValueObjects;

/**
 * @property int $value
 */
final readonly class CartId
{
    public function __construct(public int $value) {}
}
