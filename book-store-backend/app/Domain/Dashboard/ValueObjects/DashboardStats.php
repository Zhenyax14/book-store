<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\ValueObjects;

final readonly class DashboardStats
{
    public function __construct(
        public StatCard $todayRevenue,
        public StatCard $newOrders,
        public StatCard $activeReaders,
        public StatCard $activeSubscriptions,
    ) {}
}
