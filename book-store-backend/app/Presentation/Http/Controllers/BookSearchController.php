<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Catalog\UseCases\SearchBooks\SearchBooksCommand;
use App\Application\Catalog\UseCases\SearchBooks\SearchBooksHandler;
use App\Presentation\Http\Resources\Catalog\BookSearchResource;
use App\Presentation\Http\Requests\Catalog\BookSearchRequest;
use Illuminate\Http\JsonResponse;

final class BookSearchController extends Controller
{
    public function __construct(
        private readonly SearchBooksHandler $handler,
    ) {}

    /**
     * @response array{
     *     data: array<int, array{
     *         id: int,
     *         title: string,
     *         slug: string,
     *         description: string|null,
     *         access_type: string,
     *         status: string,
     *         ranking_score: float
     *     }>,
     *     meta: array{
     *         total: int,
     *         limit: int,
     *         offset: int,
     *         processing_time_ms: int
     *     }
     * }
     */
    public function __invoke(BookSearchRequest $request): JsonResponse
    {
        $result = $this->handler->handle(
            SearchBooksCommand::fromArray($request->validated()),
        );

        return new JsonResponse(new BookSearchResource($result));
    }
}
