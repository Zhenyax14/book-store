<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Interfaces;

use App\Domain\Dashboard\ValueObjects\ChartPoint;
use DateTimeImmutable;

interface DashboardRepositoryInterface
{
    public function getTodayRevenueInCents(): int;

    public function getYesterdayRevenueInCents(): int;

    public function getTodayOrdersCount(): int;

    public function getYesterdayOrdersCount(): int;

    public function getActiveReadersThisWeekCount(): int;

    public function getActiveReadersLastWeekCount(): int;

    public function getActiveSubscriptionsCount(): int;

    public function getNewSubscriptionsThisMonthCount(): int;

    /** @return ChartPoint[] */
    public function getReadingSessionsChart(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): array;
}
