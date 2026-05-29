<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Reading\UseCases\GetBookChapter\GetBookChapterCommand;
use App\Application\Reading\UseCases\GetBookChapter\GetBookChapterHandler;
use App\Presentation\Http\Resources\Reading\BookChapterResource;
use Illuminate\Http\JsonResponse;

final class ReadingBookChapterController extends Controller
{
    public function __construct(private readonly GetBookChapterHandler $handler) {}

    public function __invoke(int $bookId, int $chapterId)
    {
        $command = new GetBookChapterCommand($chapterId);

        $result = $this->handler->handle($command);

        return new JsonResponse(
            new BookChapterResource($result->chapter),
        );
    }
}
