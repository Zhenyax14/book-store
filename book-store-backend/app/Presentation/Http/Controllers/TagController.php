<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Catalog\UseCases\GetTags\ListTagsCommand;
use App\Application\Catalog\UseCases\GetTags\ListTagsHandler;
use App\Presentation\Http\Requests\Catalog\ListTagsRequest;
use App\Presentation\Http\Resources\Catalog\TagCollectionResource;
use Illuminate\Http\JsonResponse;

final class TagController extends Controller
{
    public function __construct(
        private readonly ListTagsHandler  $listHandler,
    ) {}

    /**
     * @response array{
     *     data: array<int, array{
     *         id: int,
     *         name: string,
     *         slug: string
     *     }>,
     *     meta: array{
     *         total: int,
     *         per_page: int,
     *         current_page: int,
     *         total_pages: int
     *     }
     * }
     */
    public function index(ListTagsRequest $request): JsonResponse
    {
        $result = $this->listHandler->handle(
            ListTagsCommand::fromArray($request->validated()),
        );

        return new JsonResponse(
            new TagCollectionResource($result->collection),
        );
    }
}
