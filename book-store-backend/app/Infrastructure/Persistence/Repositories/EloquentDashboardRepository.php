<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Cart\Enums\CartStatusEnum;
use App\Domain\Dashboard\Interfaces\DashboardRepositoryInterface;
use App\Domain\Dashboard\ValueObjects\ChartPoint;
use DateMalformedStringException;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

final class EloquentDashboardRepository implements DashboardRepositoryInterface
{
    public function getTodayRevenueInCents(): int
    {
        return (int) DB::table('cart_items')
            ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
            ->where('carts.status', CartStatusEnum::CHECKED_OUT->value)
            ->whereDate('carts.checked_out_at', today())
            ->sum('cart_items.price');
    }

    public function getYesterdayRevenueInCents(): int
    {
        return (int) DB::table('cart_items')
            ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
            ->where('carts.status', CartStatusEnum::CHECKED_OUT->value)
            ->whereDate('carts.checked_out_at', today()->subDay())
            ->sum('cart_items.price');
    }

    public function getTodayOrdersCount(): int
    {
        return (int) DB::table('carts')
            ->where('status', CartStatusEnum::CHECKED_OUT->value)
            ->whereDate('checked_out_at', today())
            ->count();
    }

    public function getYesterdayOrdersCount(): int
    {
        return (int) DB::table('carts')
            ->where('status', CartStatusEnum::CHECKED_OUT->value)
            ->whereDate('checked_out_at', today()->subDay())
            ->count();
    }

    public function getActiveReadersThisWeekCount(): int
    {
        return (int) DB::table('reading_sessions')
            ->where('started_at', '>=', now()->startOfWeek())
            ->distinct('user_id')
            ->count('user_id');
    }

    public function getActiveReadersLastWeekCount(): int
    {
        return (int) DB::table('reading_sessions')
            ->whereBetween('started_at', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek(),
            ])
            ->distinct('user_id')
            ->count('user_id');
    }

    public function getActiveSubscriptionsCount(): int
    {
        return (int) DB::table('user_subscriptions')
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();
    }

    public function getNewSubscriptionsThisMonthCount(): int
    {
        return (int) DB::table('user_subscriptions')
            ->where('started_at', '>=', now()->startOfMonth())
            ->count();
    }

    /** @return ChartPoint[]
     * @throws DateMalformedStringException
     */
    public function getReadingSessionsChart(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): array {
        $rows = DB::table('reading_sessions')
            ->selectRaw('DATE(started_at) as date')
            ->selectRaw('COUNT(*) as sessions')
            ->selectRaw('COALESCE(SUM(pages_read), 0) as pages_read')
            ->selectRaw('COALESCE(SUM(duration_seconds), 0) as duration_seconds')
            ->whereBetween('started_at', [
                $from->format('Y-m-d H:i:s'),
                $to->format('Y-m-d H:i:s'),
            ])
            ->groupByRaw('DATE(started_at)')
            ->orderBy('date')
            ->get();

        $indexed = $rows->keyBy('date')->toArray();
        $points = [];

        $cursor = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $from->format('Y-m-d 00:00:00'));

        while ($cursor <= $to) {
            $date = $cursor->format('Y-m-d');
            $row = $indexed[$date] ?? null;
            $points[] = new ChartPoint(
                date: $date,
                sessions: $row ? (int) $row->sessions : 0,
                pagesRead: $row ? (int) $row->pages_read : 0,
                durationSeconds: $row ? (int) $row->duration_seconds : 0,
            );
            $cursor = $cursor->modify('+1 day');
        }

        return $points;
    }
}
