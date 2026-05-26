<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Reading;

use App\Domain\Reading\Entities\BookChapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property BookChapter $resource
 */
final class BookChapterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'title' => $resource->title,
            'number' => $resource->number,
            'slug' => $resource->slug,
            'bookId' => $resource->bookId,
            'pageIds' => $resource->pageIds,
        ];
    }
}
