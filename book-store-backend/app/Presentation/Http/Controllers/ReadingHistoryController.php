<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Reading\UseCases\GetReadingHistory\GetReadingHistoryCommand;
use App\Application\Reading\UseCases\GetReadingHistory\GetReadingHistoryHandler;
use App\Presentation\Http\Resources\Reading\ReadingHistoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReadingHistoryController extends Controller
{
    public function __construct(
        private readonly GetReadingHistoryHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->handler->handle(
            new GetReadingHistoryCommand(userId: $request->user()->id),
        );

        return new JsonResponse(new ReadingHistoryResource($result));
    }
}
