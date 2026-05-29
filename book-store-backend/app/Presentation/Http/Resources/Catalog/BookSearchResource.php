<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Catalog;

use App\Application\Catalog\DTOs\BookSearchHit;
use App\Application\Catalog\DTOs\BookSearchResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property BookSearchResult $resource
 * @property BookSearchHit[] $data
 * @property int $total
 * @property int $limit
 * @property int $offset
 * @property int $processingTimeMs
 */
final class BookSearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $result = $this->resource;

        return [
            'data' => array_map(
                static fn(BookSearchHit $hit) => [
                    'id'            => $hit->bookId,
                    'title'         => $hit->title,
                    'slug'          => $hit->slug,
                    'description'   => $hit->description,
                    'access_type'   => $hit->accessType,
                    'status'        => $hit->status,
                    'ranking_score' => $hit->rankingScore,
                ],
                $result->hits,
            ),
            'meta' => [
                'total'              => $result->total,
                'limit'              => $result->limit,
                'offset'             => $result->offset,
                'processing_time_ms' => $result->processingTimeMs,
            ],
        ];
    }
}
