<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases\GetReadingSessionsChart;

use App\Domain\Dashboard\Enums\PeriodEnum;

final readonly class GetReadingSessionsChartCommand
{
    public function __construct(
        public PeriodEnum $period,
    ) {}
}
