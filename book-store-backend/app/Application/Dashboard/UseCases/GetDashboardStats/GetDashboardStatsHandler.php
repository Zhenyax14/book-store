<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases\GetDashboardStats;

use App\Domain\Dashboard\Enums\StatCardTypeEnum;
use App\Domain\Dashboard\Interfaces\DashboardRepositoryInterface;
use App\Domain\Dashboard\ValueObjects\DashboardStats;
use App\Domain\Dashboard\ValueObjects\StatCard;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;

final readonly class GetDashboardStatsHandler
{
    public function __construct(
        private DashboardRepositoryInterface $repository,
    ) {}

    public function handle(): GetDashboardStatsResult
    {
        $currency = new Currency('USD');

        $todayRevenue     = new Money($this->repository->getTodayRevenueInCents(), $currency);
        $yesterdayRevenue = new Money($this->repository->getYesterdayRevenueInCents(), $currency);

        $todayOrders     = $this->repository->getTodayOrdersCount();
        $yesterdayOrders = $this->repository->getYesterdayOrdersCount();

        $activeReadersNow  = $this->repository->getActiveReadersThisWeekCount();
        $activeReadersLast = $this->repository->getActiveReadersLastWeekCount();

        $activeSubs    = $this->repository->getActiveSubscriptionsCount();
        $newSubsMonth  = $this->repository->getNewSubscriptionsThisMonthCount();

        $stats = new DashboardStats(
            todayRevenue: new StatCard(
                label: StatCardTypeEnum::TODAY_REVENUE,
                value: $todayRevenue->format(),
                delta: $this->formatRevenueDelta($todayRevenue, $yesterdayRevenue),
                isUp: $todayRevenue->amount >= $yesterdayRevenue->amount,
            ),
            newOrders: new StatCard(
                label: StatCardTypeEnum::NEW_ORDERS,
                value: (string) $todayOrders,
                delta: $this->formatCountDelta($todayOrders - $yesterdayOrders, 'vs yesterday'),
                isUp: $todayOrders >= $yesterdayOrders,
            ),
            activeReaders: new StatCard(
                label: StatCardTypeEnum::ACTIVE_READERS,
                value: (string) $activeReadersNow,
                delta: $this->formatCountDelta($activeReadersNow - $activeReadersLast, 'vs last week'),
                isUp: $activeReadersNow >= $activeReadersLast,
            ),
            activeSubscriptions: new StatCard(
                label: StatCardTypeEnum::ACTIVE_SUBSCRIPTIONS,
                value: (string) $activeSubs,
                delta: "+{$newSubsMonth} this month",
                isUp: $newSubsMonth >= 0,
            ),
        );

        return new GetDashboardStatsResult($stats);
    }

    private function formatRevenueDelta(Money $today, Money $yesterday): string
    {
        if (0 === $yesterday->amount) {
            return '+100% vs yesterday';
        }

        $percent = round(($today->amount - $yesterday->amount) / $yesterday->amount * 100, 1);
        $sign    = $percent >= 0 ? '+' : '';

        return "{$sign}{$percent}% vs yesterday";
    }

    private function formatCountDelta(int $diff, string $suffix): string
    {
        $sign = $diff >= 0 ? '+' : '';

        return "{$sign}{$diff} {$suffix}";
    }
}
