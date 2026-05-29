<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\User\UseCases\GetReader\GetReaderCommand;
use App\Application\User\UseCases\GetReader\GetReaderHandler;
use App\Application\User\UseCases\ListReaders\ListReadersCommand;
use App\Application\User\UseCases\ListReaders\ListReadersHandler;
use App\Presentation\Http\Requests\Reader\ListReadersRequest;
use App\Presentation\Http\Resources\Reader\ReaderCollectionResource;
use App\Presentation\Http\Resources\Reader\ReaderResource;
use Illuminate\Http\JsonResponse;

final class ReaderController extends Controller
{
    public function __construct(
        private readonly ListReadersHandler $listReadersHandler,
        private readonly GetReaderHandler   $getReaderHandler,
    ) {}

    /**
     * @response array{
     *     data: array<int, array{
     *         id: int,
     *         name: string,
     *         email: string,
     *         has_active_subscription: bool,
     *         has_books: bool,
     *         created_at: string,
     *     }>,
     *     meta: array{
     *         total: int,
     *         per_page: int,
     *         current_page: int,
     *         total_pages: int
     *     }
     * }
     */
    public function index(ListReadersRequest $request): JsonResponse
    {
        $result = $this->listReadersHandler->handle(
            ListReadersCommand::fromArray($request->validated()),
        );

        return new JsonResponse(
            new ReaderCollectionResource($result->collection),
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @response array{
     *     id: int,
     *     name: string,
     *     email: string,
     *     has_active_subscription: bool,
     *     has_books: bool,
     *     created_at: string,
     * }
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->getReaderHandler->handle(
            new GetReaderCommand(
                userId: $id,
            ),
        );

        return new JsonResponse(
            new ReaderResource($result->reader),
        );
    }
}
