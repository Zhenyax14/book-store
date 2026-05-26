<?php

namespace App\Domain\Reading\Interfaces;

use App\Domain\Reading\Entities\BookChapter;

interface BookChapterRepositoryInterface
{
    public function findById(int $id): ?BookChapter;

    public function findBySlug(string $bookId, string $slug): ?BookChapter;

    /** @return BookChapter[] */
    public function findByBookId(int $bookId): array;

    /** @return BookChapter[] */
    public function findPublishedByBookId(int $bookId): array;

    public function save(BookChapter $chapter): BookChapter;

    /** @param BookChapter[] $chapters */
    public function saveMany(array $chapters): void;

    public function delete(int $id): void;

    public function deleteByBookId(int $bookId): void;
}
