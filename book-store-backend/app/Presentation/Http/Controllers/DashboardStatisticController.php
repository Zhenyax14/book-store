<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Dashboard\UseCases\GetDashboardStats\GetDashboardStatsHandler;
use App\Presentation\Http\Resources\Dashboard\DashboardStatsResource;
use Illuminate\Http\JsonResponse;

final class DashboardStatisticController extends Controller
{
    public function __construct(
        private readonly GetDashboardStatsHandler $handler,
    ) {}

    /**
     * @response array{
     *     today_revenue: array{label: string, value: string, delta: string, is_up: bool},
     *     new_orders: array{label: string, value: string, delta: string, is_up: bool},
     *     active_readers: array{label: string, value: string, delta: string, is_up: bool},
     *     active_subscriptions: array{label: string, value: string, delta: string, is_up: bool}
     * }
     */
    public function __invoke(): JsonResponse
    {
        $result = $this->handler->handle();

        return new JsonResponse(new DashboardStatsResource($result->stats));
    }
}
