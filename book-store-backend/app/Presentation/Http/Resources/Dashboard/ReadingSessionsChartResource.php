<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Dashboard;

use App\Application\Dashboard\UseCases\GetReadingSessionsChart\GetReadingSessionsChartResult;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/** @property GetReadingSessionsChartResult $resource */
final class ReadingSessionsChartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $result = $this->resource;

        $totalSessions = 0;
        $totalPages    = 0;
        $totalDuration = 0;

        $data = [];

        foreach ($result->points as $point) {
            $data[] = [
                'date'             => $point->date,
                'sessions'         => $point->sessions,
                'pages_read'       => $point->pagesRead,
                'duration_seconds' => $point->durationSeconds,
            ];

            $totalSessions += $point->sessions;
            $totalPages    += $point->pagesRead;
            $totalDuration += $point->durationSeconds;
        }

        return [
            'period'  => $result->period->value,
            'data'    => $data,
            'summary' => [
                'total_sessions'         => $totalSessions,
                'total_pages_read'       => $totalPages,
                'total_duration_seconds' => $totalDuration,
            ],
        ];
    }
}
