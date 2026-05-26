<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Application\Catalog\UseCases\PublishBook\PublishBookCommand;
use App\Application\Catalog\UseCases\PublishBook\PublishBookHandler;
use App\Presentation\Http\Resources\Catalog\PublishBookResource;
use Illuminate\Http\JsonResponse;

final class PublishBookController extends Controller
{
    public function __construct(
        private readonly PublishBookHandler $handler,
        private readonly BookCoverStorageInterface $storage,
    ) {}

    public function __invoke(int $bookId): JsonResponse
    {
        $result = $this->handler->handle(
            new PublishBookCommand(
                id: $bookId,
            ),
        );

        return new JsonResponse(new PublishBookResource($result->book)->withStorage($this->storage));
    }
}
