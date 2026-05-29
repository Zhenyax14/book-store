<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Subscription;

use App\Domain\Subscription\Entities\Subscription;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Subscription $resource
 */
class SubscriptionResource extends JsonResource
{
    public function toArray($request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'status' => $resource->status->value,
            'expires_at' => $resource->expiresAt,
            'started_at' => $resource->startedAt,
            'stripe_subscription_id' => $resource->stripeSubscriptionId,
            'user_id' => $resource->userId,
        ];
    }
}
