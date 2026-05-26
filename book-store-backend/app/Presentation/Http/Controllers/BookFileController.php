<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Catalog\UseCases\UploadBookFile\UploadBookFileCommand;
use App\Application\Catalog\UseCases\UploadBookFile\UploadBookFileHandler;
use App\Domain\Catalog\Enums\BookUploadStatusEnum;
use App\Presentation\Http\Requests\Catalog\UploadBookFileRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class BookFileController extends Controller
{
    public function __construct(
        private readonly UploadBookFileHandler $uploadFileHandler,
    ) {}

    /**
     * @param UploadBookFileRequest $request
     * @param int $id
     * @return JsonResponse
     *
     * @response array{
     *     book_id: int,
     *     file_path: string,
     *     status: App\Domain\Catalog\Enums\BookUploadStatusEnum
     * }
     */
    public function __invoke(UploadBookFileRequest $request, int $id): JsonResponse
    {
        $file = $request->file('book_file');

        $result = $this->uploadFileHandler->handle(
            new UploadBookFileCommand(
                bookId: $id,
                tempPath: $file->getRealPath(),
                filename: $file->getClientOriginalName(),
                mimeType: $file->getMimeType(),
            ),
        );

        return new JsonResponse([
            'book_id'   => $result->bookId,
            'file_path' => $result->filePath,
            'status'    => BookUploadStatusEnum::PROCESSING->value,
        ], Response::HTTP_ACCEPTED);
    }
}
