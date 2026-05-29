<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\DeleteBook;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Application\Catalog\Interfaces\BookFileStorageInterface;
use App\Application\Catalog\Interfaces\BookSearchIndexInterface;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;

final readonly class DeleteBookHandler
{
    public function __construct(
        private BookRepositoryInterface  $books,
        private BookSearchIndexInterface $searchIndex,
        private BookCoverStorageInterface $coverStorage,
        private BookFileStorageInterface  $fileStorage,
    ) {}

    public function handle(DeleteBookCommand $command): void
    {
        $book = $this->books->findById($command->id);

        if ( ! $book) {
            throw new BookNotFoundException($command->id);
        }

        if ($book->coverPath) {
            $this->coverStorage->delete($book->coverPath);
        }

        if ($book->filePath) {
            $this->fileStorage->deleteDirectory("books/{$command->id}");
        }

        $this->books->delete($command->id);
        $this->searchIndex->delete($command->id);
    }
}
