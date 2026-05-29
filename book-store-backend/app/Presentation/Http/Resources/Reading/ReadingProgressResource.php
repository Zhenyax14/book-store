<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Reading;

use App\Application\Reading\UseCases\GetReadingProgress\GetReadingProgressResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property GetReadingProgressResult $resource
 *
 */
final class ReadingProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $result = $this->resource;

        return [
            'progress' => [
                'book_id'     => $result->progress->bookId,
                'total_pages' => $result->progress->totalPages,
                'read_pages'  => $result->progress->readPages,
                'percentage'  => $result->progress->percentage(),
                'is_finished' => $result->isFinished,
            ],
            'last_position' => $result->lastPosition
                ? [
                    'chapter_id'      => (int) $result->lastPosition->chapterId,
                    'page_id'         => (int) $result->lastPosition->pageId,
                    'scroll_position' => (int) $result->lastPosition->scrollPosition,
                ]
                : null,
            'last_read_at' => $result->lastReadAt?->format(\DateTimeInterface::ATOM),
        ];
    }
}
