<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Order;

use App\Domain\Order\ValueObject\OrderCollection;
use App\Domain\Order\ValueObject\OrderSummary;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/** @property OrderCollection $resource */
final class OrderCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $collection = $this->resource;

        return [
            'data' => array_map(
                static fn(OrderSummary $order) => new OrderResource($order)->toArray($request),
                $collection->items,
            ),
            'meta' => [
                'total'        => $collection->total,
                'per_page'     => $collection->perPage,
                'current_page' => $collection->currentPage,
                'total_pages'  => $collection->totalPages(),
            ],
        ];
    }
}
