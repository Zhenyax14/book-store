<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Catalog\Entities\Book;
use App\Domain\Reading\Entities\Book as ReadingBook;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Domain\Reading\Entities\BookChapter;
use App\Domain\Reading\Entities\Bookmark;
use App\Domain\Reading\Interfaces\BookRepositoryInterface as ReadingBookRepositoryInterface;
use App\Domain\Catalog\ValueObjects\BookCollection;
use App\Domain\Catalog\ValueObjects\BookFilter;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;
use App\Infrastructure\Persistence\Models\BookChapterModel;
use App\Infrastructure\Persistence\Models\BookModel;
use Generator;

final class EloquentBookRepository implements BookRepositoryInterface, ReadingBookRepositoryInterface
{
    public function findById(int $id): ?Book
    {
        $model = BookModel::query()->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findForReadingById(int $bookId, int $userId): ?ReadingBook
    {
        $model = BookModel::query()
            ->with([
                'chapters' => fn($q) => $q
                    ->published()
                    ->ordered()
                    ->with([
                        'pages' => fn($q) => $q
                            ->select(['id', 'chapter_id', 'number'])
                            ->ordered(),
                    ]),
                'bookmark' => fn($q) => $q
                    ->where('book_id', $bookId)
                    ->where('user_id', $userId),
            ])
            ->find($bookId);

        return $model ? $this->toReadingDomain($model) : null;
    }

    public function findBySlug(string $slug): ?Book
    {
        $model = BookModel::query()->where('slug', $slug)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(BookFilter $filter): BookCollection
    {
        $query = BookModel::query();

        if ($filter->status) {
            $query = $query->where('status', $filter->status);
        }

        if ($filter->search) {
            $query = $query
                ->where('title', 'like', "%{$filter->search}%")
                ->orWhere('isbn', 'like', "%{$filter->search}%");
        }

        if ($filter->accessType) {
            $query = $query->byAccessType($filter->accessType);
        }

        if ($filter->language) {
            $query = $query->byLanguage($filter->language);
        }
        if ($filter->tag) {
            $query->whereHas(
                'tags',
                fn($q) => $q->where('slug', $filter->tag),
            );
        }
        $paginator = $query->paginate(
            perPage: $filter->perPage,
            page: $filter->page,
        );

        return new BookCollection(
            items: array_map(
                fn(BookModel $model) => $this->toDomain($model),
                $paginator->items(),
            ),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
        );
    }

    public function save(Book $book): Book
    {
        $bookData = [
            'title'          => $book->title,
            'slug'           => $book->slug,
            'description'    => $book->description,
            'isbn'           => $book->isbn,
            'language'       => $book->language,
            'publisher'      => $book->publisher,
            'published_year' => $book->publishedYear,
            'edition'        => $book->edition,
            'pages_count'    => $book->pagesCount,
            'cover_path'     => $book->coverPath,
            'access_type'    => $book->accessType->value,
            'price'          => $book->price->amount,
            'currency'       => $book->price->currency->code,
            'status'         => $book->status->value,
            'published_at'   => $book->publishedAt,
            'file_path'      => $book->filePath,
        ];
        if (null === $book->id) {
            $model = BookModel::create($bookData);
        } else {
            $model = BookModel::findOrFail($book->id);
            $model->update($bookData);
        }

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        BookModel::findOrFail($id)->delete();
    }

    public function cursor(): Generator
    {
        foreach (BookModel::query()->lazyById(chunkSize: 500) as $model) {
            yield $this->toDomain($model);
        }
    }

    private function toDomain(BookModel $model): Book
    {
        return new Book(
            title: $model->title,
            slug: $model->slug,
            language: $model->language,
            edition: $model->edition,
            pagesCount: $model->pages_count,
            accessType: $model->access_type,
            price: new Money($model->price, new Currency($model->currency)),
            status: $model->status,
            description: $model->description,
            isbn: $model->isbn,
            publishedAt: $model->published_at,
            coverPath: $model->cover_path,
            filePath: $model->file_path,
            publisher: $model->publisher,
            publishedYear: $model->published_year,
            id: $model->id,
        );
    }

    private function toReadingDomain(BookModel $model): ReadingBook
    {
        $bookmark = $model->bookmark?->first();

        return new ReadingBook(
            id: $model->id,
            title: $model->title,
            slug: $model->slug,
            description: $model->description,
            publisher: $model->publisher,
            chapters: $model->chapters->map(
                fn(BookChapterModel $chapter) => new BookChapter(
                    id: $chapter->id,
                    bookId: $chapter->book_id,
                    volumeId: $chapter->volume_id,
                    number: $chapter->number,
                    title: $chapter->title,
                    slug: $chapter->slug,
                    readingTimeMinutes: $chapter->reading_time_minutes,
                    isPublished: $chapter->is_published,
                    pageIds: $chapter->pages->pluck('id')->toArray(),
                ),
            )->toArray(),
            bookmark: $bookmark
                ? new Bookmark(
                    id: $bookmark->id,
                    userId: $bookmark->user_id,
                    bookId: $bookmark->book_id,
                    chapterId: $bookmark->chapter_id,
                    pageId: $bookmark->page_id,
                    label: $bookmark->label,
                    color: $bookmark->color,
                )
                : null,
        );
    }
}
