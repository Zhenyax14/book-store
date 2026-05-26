<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Reading\UseCases\AddBookToList\AddBookToListCommand;
use App\Application\Reading\UseCases\AddBookToList\AddBookToListHandler;
use App\Application\Reading\UseCases\GetReadingList\GetReadingListCommand;
use App\Application\Reading\UseCases\GetReadingList\GetReadingListHandler;
use App\Application\Reading\UseCases\RemoveBookFromList\RemoveBookFromListCommand;
use App\Application\Reading\UseCases\RemoveBookFromList\RemoveBookFromListHandler;
use App\Application\Reading\UseCases\StartReading\StartReadingCommand;
use App\Application\Reading\UseCases\StartReading\StartReadingHandler;
use App\Application\Reading\UseCases\UpdateProgress\UpdateProgressCommand;
use App\Application\Reading\UseCases\UpdateProgress\UpdateProgressHandler;
use App\Presentation\Http\Requests\Reading\ListReadingListRequest;
use App\Presentation\Http\Requests\Reading\StartReadingRequest;
use App\Presentation\Http\Requests\Reading\UpdateProgressRequest;
use App\Presentation\Http\Resources\Reading\ReadingEntryCollectionResource;
use App\Presentation\Http\Resources\Reading\ReadingEntryResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ReadingListController extends Controller
{
    public function __construct(
        private readonly GetReadingListHandler    $listHandler,
        private readonly AddBookToListHandler     $addHandler,
        private readonly StartReadingHandler      $startHandler,
        private readonly UpdateProgressHandler    $progressHandler,
        private readonly RemoveBookFromListHandler $removeHandler,
    ) {}

    /**
     * @response array{
     *     data: array<int, array{
     *         id: int|null,
     *         book_id: int,
     *         status: \App\Domain\Reading\Enums\ReadingStatusEnum,
     *         current_page: int,
     *         total_pages: int|null,
     *         progress_percentage: float|null,
     *         started_at: string|null,
     *         finished_at: string|null
     *     }>,
     *     meta: array{
     *         total: int,
     *         per_page: int,
     *         current_page: int,
     *         total_pages: int
     *     }
     * }
     */
    public function index(ListReadingListRequest $request): JsonResponse
    {
        $result = $this->listHandler->handle(
            GetReadingListCommand::fromArray($request->user()->id, $request->validated()),
        );

        return new JsonResponse(new ReadingEntryCollectionResource($result->collection));
    }

    public function store(int $bookId): JsonResponse
    {
        $result = $this->addHandler->handle(new AddBookToListCommand(
            userId: request()->user()->id,
            bookId: $bookId,
        ));

        return new JsonResponse(
            new ReadingEntryResource($result->entry),
            Response::HTTP_CREATED,
        );
    }

    public function start(StartReadingRequest $request, int $bookId): JsonResponse
    {
        $result = $this->startHandler->handle(
            StartReadingCommand::fromArray($request->user()->id, $bookId, $request->validated()),
        );

        return new JsonResponse(new ReadingEntryResource($result->entry));
    }

    public function progress(UpdateProgressRequest $request, int $bookId): JsonResponse
    {
        $result = $this->progressHandler->handle(
            UpdateProgressCommand::fromArray($request->user()->id, $bookId, $request->validated()),
        );

        return new JsonResponse(new ReadingEntryResource($result->entry));
    }

    public function destroy(int $bookId): JsonResponse
    {
        $this->removeHandler->handle(new RemoveBookFromListCommand(
            userId: request()->user()->id,
            bookId: $bookId,
        ));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
