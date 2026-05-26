<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Catalog\UseCases\AttachBookTag\AttachBookTagCommand;
use App\Application\Catalog\UseCases\AttachBookTag\AttachBookTagHandler;
use App\Application\Catalog\UseCases\DetachBookTag\DetachBookTagCommand;
use App\Application\Catalog\UseCases\DetachBookTag\DetachBookTagHandler;
use App\Application\Catalog\UseCases\SyncBookTags\SyncBookTagsCommand;
use App\Application\Catalog\UseCases\SyncBookTags\SyncBookTagsHandler;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Exceptions\TagNotFoundException;
use App\Presentation\Http\Requests\Catalog\SyncBookTagsRequest;
use Illuminate\Http\JsonResponse;

final class BookTagController extends Controller
{
    public function __construct(
        private readonly SyncBookTagsHandler  $syncHandler,
        private readonly AttachBookTagHandler $attachHandler,
        private readonly DetachBookTagHandler $detachHandler,
    ) {}

    public function sync(SyncBookTagsRequest $request, int $id): JsonResponse
    {
        try {
            $this->syncHandler->handle(
                new SyncBookTagsCommand(
                    bookId: $id,
                    tagIds: $request->validated()['tag_ids'],
                ),
            );
        } catch (BookNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (TagNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 422); // ← 422 для sync
        }
        $this->syncHandler->handle(
            new SyncBookTagsCommand(
                bookId: $id,
                tagIds: $request->validated()['tag_ids'],
            ),
        );

        return new JsonResponse(null, 204);
    }

    public function attach(int $id, int $tagId): JsonResponse
    {
        $this->attachHandler->handle(
            new AttachBookTagCommand(
                bookId: $id,
                tagId: $tagId,
            ),
        );

        return new JsonResponse(null, 204);
    }

    public function detach(int $id, int $tagId): JsonResponse
    {
        $this->detachHandler->handle(
            new DetachBookTagCommand(
                bookId: $id,
                tagId: $tagId,
            ),
        );

        return new JsonResponse(null, 204);
    }
}
