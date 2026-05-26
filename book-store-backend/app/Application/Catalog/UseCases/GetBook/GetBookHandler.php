<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\GetBook;

use App\Application\Catalog\DTOs\BookFileLink;
use App\Application\Catalog\Interfaces\BookFileStorageInterface;
use App\Domain\Catalog\Entities\Book;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;

final readonly class GetBookHandler
{
    public function __construct(
        private BookRepositoryInterface $books,
        private BookFileStorageInterface    $fileStorage,
    ) {}

    public function handle(GetBookCommand $command): GetBookResult
    {
        $book = $this->books->findById($command->id);

        if ( ! $book) {
            throw new BookNotFoundException($command->id);
        }

        return new GetBookResult(
            book: $book,
            fileLinks: $this->resolveFileLinks($book),
        );
    }

    private function resolveFileLinks(Book $book): array
    {
        $files = $this->fileStorage->listFiles("books/{$book->id}");

        return array_map(
            fn(string $path) => new BookFileLink(
                mimeType: $this->resolveMimeType($path),
                url: $this->fileStorage->url($path),
                label: mb_strtoupper(pathinfo($path, PATHINFO_EXTENSION)),
            ),
            $files,
        );
    }

    private function resolveMimeType(string $filePath): string
    {
        return match (pathinfo($filePath, PATHINFO_EXTENSION)) {
            'epub'  => 'application/epub+zip',
            'pdf'   => 'application/pdf',
            default => 'application/octet-stream',
        };
    }
}
