<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Dashboard\UseCases\GetReadingSessionsChart\GetReadingSessionsChartCommand;
use App\Application\Dashboard\UseCases\GetReadingSessionsChart\GetReadingSessionsChartHandler;
use App\Domain\Dashboard\Enums\PeriodEnum;
use App\Presentation\Http\Requests\Dashboard\GetReadingSessionsChartRequest;
use App\Presentation\Http\Resources\Dashboard\ReadingSessionsChartResource;
use DateMalformedStringException;
use Illuminate\Http\JsonResponse;

final class DashboardChartReadingSessionController extends Controller
{
    public function __construct(
        private readonly GetReadingSessionsChartHandler $handler,
    ) {}

    /**
     * @response array{
     *     period: string,
     *     data: array<int, array{
     *         date: string,
     *         sessions: int,
     *         pages_read: int,
     *         duration_seconds: int
     *     }>,
     *     summary: array{
     *         total_sessions: int,
     *         total_pages_read: int,
     *         total_duration_seconds: int
     *     }
     * }
     * @throws DateMalformedStringException
     */
    public function __invoke(GetReadingSessionsChartRequest $request): JsonResponse
    {
        $period = PeriodEnum::from($request->validated('period', 'week'));

        $result = $this->handler->handle(
            new GetReadingSessionsChartCommand($period),
        );

        return new JsonResponse(new ReadingSessionsChartResource($result));
    }
}
