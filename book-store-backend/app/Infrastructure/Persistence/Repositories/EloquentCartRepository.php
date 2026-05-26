<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Cart\Entities\Cart;
use App\Domain\Cart\Entities\CartItem;
use App\Domain\Cart\Enums\CartStatusEnum;
use App\Domain\Cart\Interfaces\CartRepositoryInterface;
use App\Domain\Cart\ValueObjects\CartId;
use App\Domain\Cart\ValueObjects\CartItemId;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;
use App\Infrastructure\Persistence\Models\CartItemModel;
use App\Infrastructure\Persistence\Models\CartModel;
use Illuminate\Support\Facades\DB;
use Throwable;

final class EloquentCartRepository implements CartRepositoryInterface
{
    public function findActiveByUser(int $userId): ?Cart
    {
        $model = CartModel::query()
            ->with('items')
            ->where('user_id', $userId)
            ->where('status', CartStatusEnum::ACTIVE)
            ->latest()
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findById(int $cartId): ?Cart
    {
        $model = CartModel::query()->with('items')->find($cartId);

        return $model ? $this->toDomain($model) : null;
    }

    /**
     * @throws Throwable
     */
    public function save(Cart $cart): Cart
    {
        return DB::transaction(
            function () use ($cart): Cart {
                if (null === $cart->id) {
                    $model = CartModel::query()->create([
                        'user_id' => $cart->userId,
                        'status' => $cart->status->value,
                        'checked_out_at' => $cart->checkedOutAt,
                    ]);
                } else {
                    $model = CartModel::query()->findOrFail($cart->id->value);
                    $model->update([
                        'status' => $cart->status->value,
                        'checked_out_at' => $cart->checkedOutAt,
                    ]);
                }

                CartItemModel::query()->where('cart_id', $model->id)->delete();

                foreach ($cart->items as $item) {
                    CartItemModel::query()->create([
                        'cart_id' => $model->id,
                        'type' => $item->type->value,
                        'reference_id' => $item->referenceId,
                        'title' => $item->title,
                        'price' => $item->price->amount,
                        'currency' => $item->price->currency->code,
                    ]);
                }

                return $this->toDomain($model->load('items'));
            },
        );
    }

    private function toDomain(CartModel $model): Cart
    {
        return new Cart(
            id: new CartId($model->id),
            userId: $model->user_id,
            status: $model->status,
            items: $model->items
                ->map(fn(CartItemModel $i) => $this->itemToDomain($i))
                ->all(),
            createdAt: $model->created_at,
            checkedOutAt: $model->checked_out_at,
        );
    }

    private function itemToDomain(CartItemModel $model): CartItem
    {
        return new CartItem(
            id: new CartItemId($model->id),
            cartId: $model->cart_id,
            type: $model->type,
            referenceId: $model->reference_id,
            title: $model->title,
            price: new Money($model->price, new Currency($model->currency)),
        );
    }
}
