<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Reading;

use App\Application\Reading\UseCases\GetBookPage\GetBookPageResult;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/** @property GetBookPageResult $resource */
final class BookPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $result = $this->resource;
        $page   = $result->page;

        return [
            'page' => [
                'id'             => $page->id,
                'chapter_id'     => $page->chapterId,
                'number'         => $page->number,
                'global_number'  => $page->globalNumber,
                'content'        => $page->content,
                'content_format' => $page->contentFormat->value,
                'word_count'     => $page->wordCount,
            ],
            'adjacent' => [
                'previous_page_id' => $result->adjacent->previous?->id,
                'next_page_id'     => $result->adjacent->next?->id,
                'has_previous'     => $result->adjacent->hasPrevious(),
                'has_next'         => $result->adjacent->hasNext(),
            ],
            'progress' => [
                'book_id'     => $result->progress->bookId,
                'total_pages' => $result->progress->totalPages,
                'read_pages'  => $result->progress->readPages,
                'percentage'  => $result->progress->percentage(),
                'is_finished' => $result->progress->isFinished(),
            ],
        ];
    }
}
