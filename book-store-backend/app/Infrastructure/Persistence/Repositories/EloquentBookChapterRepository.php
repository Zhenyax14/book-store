<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Reading\Entities\BookChapter;
use App\Domain\Reading\Interfaces\BookChapterRepositoryInterface;
use App\Infrastructure\Persistence\Models\BookChapterModel;

final class EloquentBookChapterRepository implements BookChapterRepositoryInterface
{
    public function findById(int $id): ?BookChapter
    {
        $model = BookChapterModel::query()->with('pages')->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findBySlug(string $bookId, string $slug): ?BookChapter
    {
        $model = BookChapterModel::byBook($bookId)
            ->where('slug', $slug)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findByBookId(int $bookId): array
    {
        return BookChapterModel::byBook($bookId)
            ->ordered()
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findPublishedByBookId(int $bookId): array
    {
        return BookChapterModel::byBook($bookId)
            ->published()
            ->ordered()
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function save(BookChapter $chapter): BookChapter
    {
        if (null === $chapter->id) {
            $model = BookChapterModel::create($this->toArray($chapter));
        } else {
            $model = BookChapterModel::findOrFail($chapter->id);
            $model->update($this->toArray($chapter));
        }

        return $this->toDomain($model);
    }

    public function saveMany(array $chapters): void
    {
        foreach ($chapters as $chapter) {
            $this->save($chapter);
        }
    }

    public function delete(int $id): void
    {
        BookChapterModel::destroy($id);
    }

    public function deleteByBookId(int $bookId): void
    {
        BookChapterModel::byBook($bookId)->delete();
    }

    private function toArray(BookChapter $chapter): array
    {
        return [
            'book_id'               => $chapter->bookId,
            'volume_id'             => $chapter->volumeId,
            'number'                => $chapter->number,
            'title'                 => $chapter->title,
            'slug'                  => $chapter->slug,
            'reading_time_minutes'  => $chapter->readingTimeMinutes,
            'is_published'          => $chapter->isPublished,
        ];
    }

    private function toDomain(BookChapterModel $model): BookChapter
    {
        return new BookChapter(
            id: $model->id,
            bookId: $model->book_id,
            volumeId: $model->volume_id,
            number: $model->number,
            title: $model->title,
            slug: $model->slug,
            readingTimeMinutes: $model->reading_time_minutes,
            isPublished: $model->is_published,
            pageIds: $model->relationLoaded('pages')
                ? $model->pages?->pluck('id')->toArray()
                : [],
        );
    }
}
