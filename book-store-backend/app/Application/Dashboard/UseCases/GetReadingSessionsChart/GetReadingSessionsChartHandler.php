<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases\GetReadingSessionsChart;

use App\Domain\Dashboard\Enums\PeriodEnum;
use App\Domain\Dashboard\Interfaces\DashboardRepositoryInterface;
use DateMalformedStringException;
use DateTimeImmutable;

final readonly class GetReadingSessionsChartHandler
{
    public function __construct(
        private DashboardRepositoryInterface $repository,
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public function handle(GetReadingSessionsChartCommand $command): GetReadingSessionsChartResult
    {
        [$from, $to] = $this->resolveDateRange($command->period);

        $points = $this->repository->getReadingSessionsChart($from, $to);

        return new GetReadingSessionsChartResult(
            points: $points,
            period: $command->period,
        );
    }

    /**
     * @throws DateMalformedStringException
     */
    private function resolveDateRange(PeriodEnum $period): array
    {
        $to = new DateTimeImmutable('today midnight');

        return match ($period) {
            PeriodEnum::DAY   => [
                $to->modify('-23 hours'),
                $to->modify('+1 hour')->modify('-1 second'),
            ],
            PeriodEnum::WEEK  => [
                $to->modify('-6 days'),
                $to->modify('+1 day')->modify('-1 second'),
            ],
            PeriodEnum::MONTH => [
                $to->modify('-29 days'),
                $to->modify('+1 day')->modify('-1 second'),
            ],
            PeriodEnum::YEAR  => [
                $to->modify('-364 days'),
                $to->modify('+1 day')->modify('-1 second'),
            ],
        };
    }
}
