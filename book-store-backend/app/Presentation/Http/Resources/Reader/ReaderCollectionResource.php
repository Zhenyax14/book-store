<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Reader;

use App\Domain\User\Entities\Reader;
use App\Domain\User\ValueObjects\ReaderCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @property ReaderCollection $resource
 */
final class ReaderCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $collection = $this->resource;

        return [
            'data' => array_map(
                static fn(Reader $reader) => new ReaderResource($reader),
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
