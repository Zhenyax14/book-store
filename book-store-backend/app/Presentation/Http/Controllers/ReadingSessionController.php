<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Reading\UseCases\EndReadingSession\EndReadingSessionCommand;
use App\Application\Reading\UseCases\EndReadingSession\EndReadingSessionHandler;
use App\Application\Reading\UseCases\StartReadingSession\StartReadingSessionCommand;
use App\Application\Reading\UseCases\StartReadingSession\StartReadingSessionHandler;
use App\Presentation\Http\Requests\Reading\EndReadingSessionRequest;
use App\Presentation\Http\Requests\Reading\StartReadingSessionRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ReadingSessionController extends Controller
{
    public function __construct(
        private readonly StartReadingSessionHandler $startHandler,
        private readonly EndReadingSessionHandler   $endHandler,
    ) {}

    public function start(StartReadingSessionRequest $request, int $bookId): JsonResponse
    {
        $result = $this->startHandler->handle(
            new StartReadingSessionCommand(
                userId: $request->user()->id,
                bookId: $bookId,
                currentPageId: $request->validated()['current_page_id'] ?? null,
            ),
        );

        return new JsonResponse(
            [
                'session_id' => $result->sessionId,
                'is_resumed' => $result->isResumed,
            ],
            $result->isResumed ? Response::HTTP_OK : Response::HTTP_CREATED,
        );
    }

    public function end(EndReadingSessionRequest $request, int $bookId, int $sessionId): JsonResponse
    {
        $data = $request->validated();

        $result = $this->endHandler->handle(
            new EndReadingSessionCommand(
                sessionId: $sessionId,
                endPageId: $data['end_page_id'],
                durationSeconds: $data['duration_seconds'],
            ),
        );

        return new JsonResponse([
            'pages_read'       => $result->pagesRead,
            'duration_seconds' => $result->durationSeconds,
        ]);
    }
}
