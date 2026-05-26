<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases\GetReadingSessionsChart;

use App\Domain\Dashboard\Enums\PeriodEnum;
use App\Domain\Dashboard\ValueObjects\ChartPoint;

final readonly class GetReadingSessionsChartResult
{
    /**
     * @param ChartPoint[] $points
     */
    public function __construct(
        public array      $points,
        public PeriodEnum $period,
    ) {}
}
