<?php

namespace App\Domain\Reading\Interfaces;

use App\Domain\Reading\Entities\BookPage;
use App\Domain\Reading\ValueObjects\AdjacentPages;

interface BookPageRepositoryInterface
{
    public function findById(int $id): ?BookPage;

    public function findByChapterAndNumber(int $chapterId, int $number): ?BookPage;

    public function findAdjacentPages(int $pageId): AdjacentPages;

    public function findByGlobalNumber(int $bookId, int $globalNumber): ?BookPage;

    /** @return BookPage[] */
    public function findByChapterId(int $chapterId): array;

    public function save(BookPage $page): BookPage;

    /** @param BookPage[] $pages */
    public function saveMany(array $pages): void;

    public function delete(int $id): void;

    public function deleteByChapterId(int $chapterId): void;

    public function deleteByBookId(int $bookId): void;

    public function countByBookId(int $bookId): int;
}
