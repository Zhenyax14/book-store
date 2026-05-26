<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Order;

use App\Domain\Order\ValueObject\OrderItem;
use App\Domain\Order\ValueObject\OrderSummary;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property OrderSummary $resource */
final class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $order = $this->resource;

        return [
            'id' => $order->cartId,
            'user' => [
                'id' => $order->userId,
                'email' => $order->userEmail,
                'name' => $order->userName,
            ],
            'items' => array_map(
                static fn(OrderItem $item) => [
                    'type' => $item->type->value,
                    'reference_id' => $item->referenceId,
                    'title' => $item->title,
                    'price' => $item->price->toArray(),
                    'access_granted' => $item->accessGranted,
                ],
                $order->items,
            ),
            'total' => $order->total->toArray(),
            'item_count' => $order->itemCount(),
            'stripe_payment_intent' => $order->stripePaymentIntentId,
            'checked_out_at' => $order->checkedOutAt->format(DateTimeInterface::ATOM),
        ];
    }
}
