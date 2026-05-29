<?php

namespace App\Application\Catalog\UseCases\ListBooks;

use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Domain\Catalog\ValueObjects\BookFilter;

final readonly class ListBooksHandler
{
    public function __construct(
        private BookRepositoryInterface $books,
    ) {}

    public function handle(ListBooksCommand $command): ListBooksResult
    {
        $filter = new BookFilter(
            status: $command->status ?? null,
            accessType: $command->accessType ?? null,
            language: $command->language ?? null,
            search: $command->search ?? null,
            perPage: $command->perPage,
            page: $command->page,
            tag: $command->tag,
        );

        $collection = $this->books->findAll($filter);

        return new ListBooksResult(collection: $collection);
    }
}
