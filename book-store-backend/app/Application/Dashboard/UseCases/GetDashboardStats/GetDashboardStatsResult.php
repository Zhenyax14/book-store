<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases\GetDashboardStats;

use App\Domain\Dashboard\ValueObjects\DashboardStats;

final readonly class GetDashboardStatsResult
{
    public function __construct(
        public DashboardStats $stats,
    ) {}
}
