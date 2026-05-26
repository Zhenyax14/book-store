<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Reading\UseCases\GetReadingProgress\GetReadingProgressCommand;
use App\Application\Reading\UseCases\GetReadingProgress\GetReadingProgressHandler;
use App\Application\Reading\UseCases\SaveReadingProgress\SaveReadingProgressCommand;
use App\Application\Reading\UseCases\SaveReadingProgress\SaveReadingProgressHandler;
use App\Presentation\Http\Requests\Reading\SaveReadingProgressRequest;
use App\Presentation\Http\Resources\Reading\ReadingProgressResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ReadingProgressController extends Controller
{
    public function __construct(
        private readonly GetReadingProgressHandler  $getHandler,
        private readonly SaveReadingProgressHandler $saveHandler,
    ) {}

    public function show(Request $request, int $bookId): JsonResponse
    {
        $result = $this->getHandler->handle(
            new GetReadingProgressCommand(
                userId: $request->user()->id,
                bookId: $bookId,
            ),
        );

        return new JsonResponse(new ReadingProgressResource($result));
    }

    public function save(SaveReadingProgressRequest $request, int $bookId): JsonResponse
    {
        $data = $request->validated();

        $result = $this->saveHandler->handle(
            new SaveReadingProgressCommand(
                userId: $request->user()->id,
                bookId: $bookId,
                chapterId: $data['chapter_id'],
                pageId: $data['page_id'],
                globalPageNumber: $data['global_page_number'],
                scrollPosition: $data['scroll_position'],
                totalPages: $data['total_pages'],
                bookTitle: $data['book_title'],
            ),
        );

        return new JsonResponse([
            'completion_percentage' => $result->completionPercentage,
            'is_finished'           => $result->isFinished,
        ], Response::HTTP_OK);
    }
}
