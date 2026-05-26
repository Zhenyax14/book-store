<?php

namespace App\Domain\Catalog\Interfaces;

use App\Domain\Catalog\Entities\Book;
use App\Domain\Catalog\ValueObjects\BookCollection;
use App\Domain\Catalog\ValueObjects\BookFilter;

interface BookRepositoryInterface
{
    public function findById(int $id): ?Book;

    public function findBySlug(string $slug): ?Book;

    public function findAll(BookFilter $filter): BookCollection;

    public function save(Book $book): Book;

    public function delete(int $id): void;

    public function cursor(): \Generator;
}
