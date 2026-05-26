<?php

declare(strict_types=1);

namespace App\Application\Cart\UseCases\GetCart;

use App\Domain\Cart\Entities\Cart;
use App\Domain\Cart\Interfaces\CartRepositoryInterface;

final readonly class GetCartHandler
{
    public function __construct(
        private CartRepositoryInterface $carts,
    ) {}

    public function handle(GetCartCommand $command): ?Cart
    {
        return $this->carts->findActiveByUser($command->userId);
    }
}
