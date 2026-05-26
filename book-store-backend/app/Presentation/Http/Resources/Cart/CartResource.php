<?php

namespace App\Presentation\Http\Resources\Cart;

use App\Domain\Cart\Entities\Cart;
use App\Domain\Cart\Entities\CartItem;
use App\Domain\Shared\ValueObjects\Currency;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @property Cart $resource
 */
final class CartResource extends JsonResource
{
    private string $currency;

    public function withCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $cart = $this->resource;
        $currency = new Currency($this->currency ?? 'EUR');

        return [
            'id'            => $cart->id?->value,
            'status'        => $cart->status->value,
            'items' => array_map(
                static fn(CartItem $item) => [
                    'type'         => $item->type->value,
                    'reference_id' => $item->referenceId,
                    'title'        => $item->title,
                    'price'        => [
                        'amount'    => $item->price->amount,
                        'currency'  => $item->price->currency->code,
                        'formatted' => $item->price->format(),
                    ],
                ],
                $cart->items,
            ),
            'total'         => [
                'amount'    => $cart->total($currency)->amount,
                'currency'  => $currency->code,
                'formatted' => $cart->total($currency)->format(),
            ],
            'items_count'   => count($cart->items),
            'created_at'    => $cart->createdAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
