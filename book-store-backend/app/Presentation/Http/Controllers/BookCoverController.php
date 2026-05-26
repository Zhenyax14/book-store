<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Application\Catalog\UseCases\UploadBookCover\UploadBookCoverCommand;
use App\Application\Catalog\UseCases\UploadBookCover\UploadBookCoverHandler;
use App\Presentation\Http\Requests\Catalog\UploadBookCoverRequest;
use App\Presentation\Http\Resources\Catalog\BookResource;
use Illuminate\Http\JsonResponse;

final class BookCoverController extends Controller
{
    public function __construct(
        private readonly UploadBookCoverHandler $uploadCoverHandler,
        private readonly BookCoverStorageInterface $storage,
    ) {}

    public function __invoke(UploadBookCoverRequest $request, int $id): JsonResponse
    {
        $file = $request->file('cover');

        $result = $this->uploadCoverHandler->handle(
            new UploadBookCoverCommand(
                bookId: $id,
                tempPath: $file->getRealPath(),
                filename: $file->getClientOriginalName(),
                mimeType: $file->getMimeType(),
            ),
        );

        return new JsonResponse(
            new BookResource($result->book)
                ->withStorage($this->storage),
        );
    }
}
