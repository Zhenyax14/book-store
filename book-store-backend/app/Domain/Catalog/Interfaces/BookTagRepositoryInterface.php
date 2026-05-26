<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Interfaces;

interface BookTagRepositoryInterface
{
    /** @param int[] $tagIds */
    public function sync(int $bookId, array $tagIds): void;

    public function attach(int $bookId, int $tagId): void;

    public function detach(int $bookId, int $tagId): void;

    public function detachAll(int $bookId): void;
}
