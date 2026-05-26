<?php

declare(strict_types=1);

namespace App\Application\Cart\UseCases\AddItem;

use App\Domain\Cart\Entities\Cart;
use App\Domain\Cart\Entities\CartItem;
use App\Domain\Cart\Interfaces\CartItemPriceResolverInterface;
use App\Domain\Cart\Interfaces\CartRepositoryInterface;

final readonly class AddItemToCartHandler
{
    public function __construct(
        private CartRepositoryInterface        $carts,
        private CartItemPriceResolverInterface $priceResolver,
    ) {}

    public function handle(AddItemToCartCommand $command): Cart
    {
        $cart = $this->carts->findActiveByUser($command->userId)
            ?? Cart::create($command->userId);

        ['title' => $title, 'price' => $price] = $this->priceResolver->resolve(
            $command->type,
            $command->referenceId,
        );

        $item = new CartItem(
            id: null,
            cartId: $cart->id?->value ?? 0,
            type: $command->type,
            referenceId: $command->referenceId,
            title: $title,
            price: $price,
        );

        return $this->carts->save($cart->addItem($item));
    }
}
