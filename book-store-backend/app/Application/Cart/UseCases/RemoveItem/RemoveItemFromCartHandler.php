<?php

declare(strict_types=1);

namespace App\Application\Cart\UseCases\RemoveItem;

use App\Domain\Cart\Entities\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Interfaces\CartRepositoryInterface;

final readonly class RemoveItemFromCartHandler
{
    public function __construct(
        private CartRepositoryInterface $carts,
    ) {}

    public function handle(RemoveItemFromCartCommand $command): Cart
    {
        $cart = $this->carts->findActiveByUser($command->userId)
            ?? throw new CartNotFoundException($command->userId);

        return $this->carts->save(
            $cart->removeItem($command->type, $command->referenceId),
        );
    }
}
