<?php

declare(strict_types=1);

namespace App\Application\Cart\UseCases\GetCart;

final readonly class GetCartCommand
{
    public function __construct(public int $userId) {}
}
