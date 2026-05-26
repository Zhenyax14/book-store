<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Application\Catalog\UseCases\GetPopularBooks\GetPopularBooksCommand;
use App\Application\Catalog\UseCases\GetPopularBooks\GetPopularBooksHandler;
use App\Presentation\Http\Requests\Catalog\PopularBooksRequest;
use App\Presentation\Http\Resources\Catalog\BookCollectionResource;
use Illuminate\Http\JsonResponse;

final class PopularBooksController extends Controller
{
    public function __construct(
        private readonly GetPopularBooksHandler $handler,
        private readonly BookCoverStorageInterface $storage,
    ) {}

    /**
     * @response array{
     *     data: array<int, array{
     *         id: int,
     *         title: string,
     *         slug: string,
     *         description: string|null,
     *         isbn: string|null,
     *         language: string,
     *         publisher: string|null,
     *         published_year: int|null,
     *         pages_count: int,
     *         cover_url: string|null,
     *         file_links: array<int, array{
     *             mime_type: string,
     *             url: string,
     *             label: string
     *         }>,
     *         access_type: App\Domain\Catalog\Enums\AccessTypeEnum,
     *         price: array{
     *             currency: string,
     *             amount: int,
     *             formatted: string
     *         },
     *         status: App\Domain\Catalog\Enums\BookStatusEnum,
     *         is_free: bool,
     *         published_at: string|null
     *     }>,
     *     meta: array{
     *         total: int,
     *         per_page: int,
     *         current_page: int,
     *         total_pages: int
     *     }
     * }
     */
    public function __invoke(PopularBooksRequest $request): JsonResponse
    {
        $result = $this->handler->handle(
            GetPopularBooksCommand::fromArray($request->validated()),
        );

        return new JsonResponse(
            new BookCollectionResource($result->collection)
                ->withStorage($this->storage)
                ->toArray($request),
        );
    }
}
