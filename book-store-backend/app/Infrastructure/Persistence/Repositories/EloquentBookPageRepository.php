<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Reading\Entities\BookPage;
use App\Domain\Reading\Enums\ContentFormatEnum;
use App\Domain\Reading\Interfaces\BookPageRepositoryInterface;
use App\Domain\Reading\ValueObjects\AdjacentPages;
use App\Infrastructure\Persistence\Models\BookPageModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EloquentBookPageRepository implements BookPageRepositoryInterface
{
    public function findById(int $id): ?BookPage
    {
        $model = BookPageModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByGlobalNumber(int $bookId, int $globalNumber): ?BookPage
    {
        $model = BookPageModel::whereHas(
            'chapter',
            static fn($q) => $q->where('book_id', $bookId),
        )
            ->where('global_number', $globalNumber)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findByChapterId(int $chapterId): array
    {
        return BookPageModel::where('chapter_id', $chapterId)
            ->orderBy('number')
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findAdjacentPages(int $pageId): AdjacentPages
    {
        $current = BookPageModel::findOrFail($pageId);

        $prev = BookPageModel::whereHas(
            'chapter',
            static fn($q) => $q->where('book_id', $current->chapter->book_id),
        )
            ->where('global_number', $current->global_number - 1)
            ->first();

        $next = BookPageModel::whereHas(
            'chapter',
            static fn($q) => $q->where('book_id', $current->chapter->book_id),
        )
            ->where('global_number', $current->global_number + 1)
            ->first();

        return new AdjacentPages(
            previous: $prev ? $this->toDomain($prev) : null,
            next: $next ? $this->toDomain($next) : null,
        );
    }

    public function save(BookPage $page): BookPage
    {
        if (null === $page->id) {
            $model = BookPageModel::create([
                'chapter_id'     => $page->chapterId,
                'number'         => $page->number,
                'global_number'  => $page->globalNumber,
                'content'        => $page->content,
                'content_format' => $page->contentFormat->value,
                'word_count'     => $page->wordCount,
            ]);
        } else {
            $model = BookPageModel::findOrFail($page->id);
            $model->update([
                'chapter_id'     => $page->chapterId,
                'number'         => $page->number,
                'global_number'  => $page->globalNumber,
                'content'        => $page->content,
                'content_format' => $page->contentFormat->value,
                'word_count'     => $page->wordCount,
            ]);
        }

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        BookPageModel::destroy($id);
    }

    public function deleteByChapterId(int $chapterId): void
    {
        BookPageModel::where('chapter_id', $chapterId)->delete();
    }

    public function findByChapterAndNumber(int $chapterId, int $number): ?BookPage
    {
        $model = BookPageModel::where('chapter_id', $chapterId)
            ->where('number', $number)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function saveMany(array $pages): void
    {
        $chunks = array_chunk($pages, 500);

        foreach ($chunks as $chunk) {
            BookPageModel::insert(
                array_map(fn(BookPage $page) => array_merge(
                    $this->toArray($page),
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ), $chunk),
            );
        }
    }

    public function deleteByBookId(int $bookId): void
    {
        BookPageModel::whereHas(
            'chapter',
            static fn(BelongsTo $q) => $q->where('book_id', $bookId),
        )->delete();
    }

    public function countByBookId(int $bookId): int
    {
        return BookPageModel::whereHas(
            'chapter',
            static fn(BelongsTo $q) => $q->where('book_id', $bookId),
        )->count();
    }

    private function toDomain(BookPageModel $model): BookPage
    {
        return new BookPage(
            id: $model->id,
            chapterId: $model->chapter_id,
            number: $model->number,
            globalNumber: $model->global_number,
            content: $model->content,
            contentFormat: ContentFormatEnum::from($model->content_format),
            wordCount: $model->word_count,
        );
    }

    private function toArray(BookPage $page): array
    {
        return [
            'chapter_id'     => $page->chapterId,
            'number'         => $page->number,
            'global_number'  => $page->globalNumber,
            'content'        => $page->content,
            'content_format' => $page->contentFormat->value,
            'word_count'     => $page->wordCount,
        ];
    }
}
