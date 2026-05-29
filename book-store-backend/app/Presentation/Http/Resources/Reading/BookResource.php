<?php

namespace App\Presentation\Http\Resources\Reading;

use App\Domain\Reading\Entities\Book;
use App\Domain\Reading\Entities\BookChapter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @property Book $resource
 * @property BookChapter[] $chapters
 */
class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'title' => $resource->title,
            'slug' => $resource->slug,
            'description' => $resource->description,
            'bookmark' => new BookmarkResource($resource->bookmark),
            'chapters' => array_map(
                static fn(BookChapter $chapter) => new BookChapterResource($chapter)->toArray($request),
                $resource->chapters,
            ),
        ];
    }
}
