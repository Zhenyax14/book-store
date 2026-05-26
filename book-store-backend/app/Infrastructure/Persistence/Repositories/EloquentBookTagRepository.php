<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Catalog\Interfaces\BookTagRepositoryInterface;
use App\Infrastructure\Persistence\Models\BookModel;

final class EloquentBookTagRepository implements BookTagRepositoryInterface
{
    public function sync(int $bookId, array $tagIds): void
    {
        BookModel::findOrFail($bookId)->tags()->sync($tagIds);
    }

    public function attach(int $bookId, int $tagId): void
    {
        BookModel::findOrFail($bookId)->tags()->syncWithoutDetaching([$tagId]);
    }

    public function detach(int $bookId, int $tagId): void
    {
        BookModel::findOrFail($bookId)->tags()->detach($tagId);
    }

    public function detachAll(int $bookId): void
    {
        BookModel::findOrFail($bookId)->tags()->detach();
    }
}
