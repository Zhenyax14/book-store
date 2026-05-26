<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Cart\Enums\CartStatusEnum;
use App\Domain\Order\Enums\OrderItemTypeEnum;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Order\ValueObject\OrderCollection;
use App\Domain\Order\ValueObject\OrderFilter;
use App\Domain\Order\ValueObject\OrderItem;
use App\Domain\Order\ValueObject\OrderSummary;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;
use App\Infrastructure\Persistence\Models\CartItemModel;
use App\Infrastructure\Persistence\Models\CartModel;
use DB;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function findOrders(OrderFilter $filter): OrderCollection
    {
        /** @var Builder $query */
        $query = CartModel::query()
            ->with(['items', 'user'])
            ->where('status', CartStatusEnum::CHECKED_OUT)
            ->orderByDesc('checked_out_at');

        $this->applySearch($query, $filter->search);
        $this->applyDateRange($query, $filter->dateFrom, $filter->dateTo);

        $paginator = $query->paginate(
            perPage: $filter->perPage,
            page: $filter->page,
        );

        $cartIds = collect($paginator->items())->pluck('id')->all();
        $accessIndex = $this->buildAccessIndex($cartIds);

        return new OrderCollection(
            items: array_map(
                fn(CartModel $cart) => $this->toDomain($cart, $accessIndex),
                $paginator->items(),
            ),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
        );
    }

    public function findById(int $cartId): OrderSummary
    {
        $cart = CartModel::query()
            ->with(['items', 'user'])
            ->where('status', 'checked_out')
            ->find($cartId);

        if (null === $cart) {
            throw new OrderNotFoundException($cartId);
        }

        $accessIndex = $this->buildAccessIndex([$cartId]);

        return $this->toDomain($cart, $accessIndex);
    }

    private function applySearch(Builder $query, ?string $search): void
    {
        if (null === $search || '' === $search) {
            return;
        }

        $query->whereHas('user', static function (Builder $q) use ($search): void {
            $q->where('email', 'ilike', "%{$search}%")
                ->orWhere('name', 'ilike', "%{$search}%");
        });
    }

    private function applyDateRange(Builder $query, ?string $from, ?string $to): void
    {
        if (null !== $from) {
            $query->where('checked_out_at', '>=', $from);
        }
        if (null !== $to) {
            $query->where('checked_out_at', '<=', $to . ' 23:59:59');
        }
    }

    /** @return array<int, array{payment_intent_id: string|null, book_ids: int[]}> */
    private function buildAccessIndex(array $cartIds): array
    {
        if (empty($cartIds)) {
            return [];
        }

        $rows = DB::table('user_book_access as uba')
            ->join('cart_items as ci', static function ($join): void {
                $join->on('ci.reference_id', '=', 'uba.book_id')
                    ->where('ci.type', '=', 'book');
            })
            ->join('carts as c', 'c.id', '=', 'ci.cart_id')
            ->whereIn('ci.cart_id', $cartIds)
            ->select('ci.cart_id', 'ci.reference_id as book_id', 'uba.stripe_payment_intent_id')
            ->get();

        $index = [];
        foreach ($rows as $row) {
            $index[$row->cart_id][$row->book_id] = $row->stripe_payment_intent_id;
        }

        return $index;
    }

    private function toDomain(CartModel $cart, array $accessIndex): OrderSummary
    {
        $currency = $cart->items->first()?->currency ?? 'EUR';
        $currencyVo = new Currency($currency);

        $items = $cart->items->map(function (CartItemModel $item) use ($cart, $accessIndex, $currencyVo): OrderItem {
            $isBook = OrderItemTypeEnum::BOOK->value === $item->type->value;
            $accessGranted = $isBook
                && isset($accessIndex[$cart->id][$item->reference_id]);

            return new OrderItem(
                type: OrderItemTypeEnum::from($item->type->value),
                referenceId: $item->reference_id,
                title: $item->title,
                price: new Money($item->price, $currencyVo),
                accessGranted: $accessGranted,
            );
        })->all();

        $total = array_reduce(
            $items,
            static fn(Money $carry, OrderItem $i) => $carry->add($i->price),
            Money::zero($currencyVo),
        );

        $paymentIntentId = null;
        foreach ($accessIndex[$cart->id] ?? [] as $intentId) {
            if (null !== $intentId) {
                $paymentIntentId = $intentId;

                break;
            }
        }

        return new OrderSummary(
            cartId: $cart->id,
            userId: $cart->user_id,
            userEmail: $cart->user->email,
            userName: $cart->user->name,
            items: $items,
            total: $total,
            checkedOutAt: $cart->checked_out_at,
            stripePaymentIntentId: $paymentIntentId,
        );
    }
}
