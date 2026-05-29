<?php

declare(strict_types=1);

namespace App\Domain\Cart\Interfaces;

use App\Domain\Cart\Entities\Cart;

interface CartRepositoryInterface
{
    public function findActiveByUser(int $userId): ?Cart;

    public function findById(int $cartId): ?Cart;

    public function save(Cart $cart): Cart;
}
