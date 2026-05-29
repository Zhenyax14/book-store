<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Reading\UseCases\GetBook\GetBookCommand;
use App\Application\Reading\UseCases\GetBook\GetBookHandler;
use App\Presentation\Http\Resources\Reading\BookResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReadingBookController extends Controller
{
    public function __construct(
        private readonly GetBookHandler $handler,
    ) {}

    /**
     * @param Request $request
     * @param int $bookId
     *
     * @return JsonResponse
     * @response array{
     *     id: int,
     *     title: string,
     *     slug: string,
     *     description: string,
     *     bookmark: {
     *         id: int,
     *         userId: int,
     *         bookId: int,
     *         chapterId: int,
     *         pageId: int,
     *         label: string,
     *         color: string,
     *     },
     *     chapters: [{
     *         id: int,
     *         title: string,
     *         slug: string,
     *         bookId: int,
     *         number: int,
     *         pageIds: array<int, int>
     *     }]
     */
    public function __invoke(Request $request, int $bookId)
    {
        $command = new GetBookCommand($bookId, $request->user()->id);

        $result = $this->handler->handle($command);

        return new JsonResponse(
            new BookResource($result->book),
        );
    }
}
