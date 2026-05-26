<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Catalog\Entities\Tag;
use App\Domain\Catalog\Interfaces\TagRepositoryInterface;
use App\Domain\Catalog\ValueObjects\TagCollection;
use App\Domain\Catalog\ValueObjects\TagFilter;
use App\Infrastructure\Persistence\Models\TagModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class EloquentTagRepository implements TagRepositoryInterface
{
    public function findById(int $id): ?Tag
    {
        $model = TagModel::query()->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findBySlug(string $slug): ?Tag
    {
        $model = TagModel::bySlug($slug)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findByBookId(int $bookId): array
    {
        return TagModel::query()->whereHas(
            'books',
            static fn(BelongsToMany $q) => $q->where('books.id', $bookId),
        )
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findByIds(array $ids): array
    {
        return TagModel::query()->whereIn('id', $ids)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function save(Tag $tag): Tag
    {
        if (null === $tag->id) {
            $model = TagModel::query()->create([
                'name' => $tag->name,
                'slug' => $tag->slug,
            ]);
        } else {
            $model = TagModel::query()->findOrFail($tag->id);
            $model->update([
                'name' => $tag->name,
                'slug' => $tag->slug,
            ]);
        }

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        TagModel::destroy($id);
    }

    public function findAll(TagFilter $filter): TagCollection
    {
        $query = TagModel::query();

        $paginator = $query->paginate(
            perPage: $filter->perPage,
            page: $filter->page,
        );

        return new TagCollection(
            items: array_map(
                fn(TagModel $model) => $this->toDomain($model),
                $paginator->items(),
            ),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
        );
    }

    private function toDomain(TagModel $model): Tag
    {
        return new Tag(
            id: $model->id,
            name: $model->name,
            slug: $model->slug,
        );
    }
}
