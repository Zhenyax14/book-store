<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Interfaces;

use App\Domain\Catalog\Entities\Tag;
use App\Domain\Catalog\ValueObjects\TagFilter;

interface TagRepositoryInterface
{
    public function findById(int $id): ?Tag;

    public function findBySlug(string $slug): ?Tag;

    public function findByBookId(int $bookId): array;

    /** @param int[] $ids */
    public function findByIds(array $ids): array;

    public function save(Tag $tag): Tag;

    public function delete(int $id): void;

    public function findAll(TagFilter $filter);
}
