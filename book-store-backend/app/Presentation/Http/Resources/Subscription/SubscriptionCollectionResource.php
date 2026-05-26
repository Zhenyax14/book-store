<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Subscription;

use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\ValueObjects\SubscriptionCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property SubscriptionCollection $resource
 */
final class SubscriptionCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $collection = $this->resource;

        return [
            'data' => array_map(
                static fn(Subscription $subscription) => new SubscriptionResource($subscription),
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
