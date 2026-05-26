<?php

namespace App\Presentation\Http\Resources\Catalog;

use App\Domain\Catalog\Entities\Tag;
use App\Domain\Catalog\ValueObjects\TagCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property TagCollection $resource
 * @property int $total
 * @property int $perPage
 * @property int $currentPage
 * @property int $totalPages
 */
final class TagCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $collection = $this->resource;

        return [
            'data' => array_map(
                static fn(Tag $book) => new TagResource($book)->toArray($request),
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
