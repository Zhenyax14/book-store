<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Reading;

use App\Domain\Reading\Entities\ReadingEntry;
use App\Domain\Reading\ValueObjects\ReadingEntryCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ReadingEntryCollection $resource
 * @property int $total
 * @property int $perPage
 * @property int $currentPage
 * @property int $totalPages
 */
final class ReadingEntryCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $collection = $this->resource;

        return [
            'data' => array_map(
                static fn(ReadingEntry $entry) => new ReadingEntryResource($entry)->toArray($request),
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
