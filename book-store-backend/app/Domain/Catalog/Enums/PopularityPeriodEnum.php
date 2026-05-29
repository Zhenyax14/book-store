<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Enums;

enum PopularityPeriodEnum: string
{
    case DAY   = 'day';
    case WEEK  = 'week';
    case MONTH = 'month';

    public function startDate(): \DateTimeImmutable
    {
        return match ($this) {
            self::DAY   => new \DateTimeImmutable('-1 day'),
            self::WEEK  => new \DateTimeImmutable('-1 week'),
            self::MONTH => new \DateTimeImmutable('-1 month'),
        };
    }
}
