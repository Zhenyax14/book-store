<?php

namespace App\Presentation\Http\Resources\Catalog;

use App\Domain\Catalog\Entities\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Tag $resource */
final class TagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tag = $this->resource;

        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
        ];
    }
}
