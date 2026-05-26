<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Dashboard;

use App\Domain\Dashboard\ValueObjects\DashboardStats;
use App\Domain\Dashboard\ValueObjects\StatCard;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/** @property DashboardStats $resource */
final class DashboardStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $stats = $this->resource;

        return [
            'today_revenue'        => $this->serializeCard($stats->todayRevenue),
            'new_orders'           => $this->serializeCard($stats->newOrders),
            'active_readers'       => $this->serializeCard($stats->activeReaders),
            'active_subscriptions' => $this->serializeCard($stats->activeSubscriptions),
        ];
    }

    private function serializeCard(StatCard $card): array
    {
        return [
            'label' => $card->label,
            'value' => $card->value,
            'delta' => $card->delta,
            'is_up' => $card->isUp,
        ];
    }
}
