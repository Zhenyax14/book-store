<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Catalog\Entities\Book;
use App\Domain\Catalog\Enums\PopularityPeriodEnum;
use App\Domain\Catalog\Interfaces\BookPopularityRepositoryInterface;
use App\Domain\Catalog\ValueObjects\BookCollection;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;
use App\Infrastructure\Persistence\Models\BookModel;

final class EloquentBookPopularityRepository implements BookPopularityRepositoryInterface
{
    public function findPopular(PopularityPeriodEnum $period, int $perPage, int $page): BookCollection
    {
        $paginator = BookModel::query()
            ->select('books.*')
            ->selectRaw('COUNT(rs.id) as popularity_score')
            ->join('reading_sessions as rs', 'books.id', '=', 'rs.book_id')
            ->published()
            ->where('rs.started_at', '>=', $period->startDate())
            ->groupBy('books.id')
            ->orderByDesc('popularity_score')
            ->paginate(perPage: $perPage, page: $page);

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
}
