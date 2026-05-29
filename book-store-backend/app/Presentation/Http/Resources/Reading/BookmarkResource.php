<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Reading;

use App\Domain\Reading\Entities\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Bookmark $resource */
final class BookmarkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'userId' => $resource->userId,
            'bookId' => $resource->bookId,
            'chapterId' => $resource->chapterId,
            'pageId' => $resource->pageId,
            'label' => $resource->label,
            'color' => $resource->color,
        ];
    }
}
