<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Reader;

use App\Domain\User\Entities\Reader;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Reader $resource */
class ReaderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'name' => $resource->name,
            'email' => $resource->email,
            'has_active_subscriptions' => $resource->hasActiveSubscription,
            'has_books' => $resource->hasBooks,
            'created_at' => $resource->created_at,
        ];
    }
}
