<?php

namespace App\Application\Catalog\UseCases\UploadBookCover;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Domain\Catalog\Entities\Book;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;

final readonly class UploadBookCoverHandler
{
    public function __construct(
        private BookRepositoryInterface   $books,
        private BookCoverStorageInterface $storage,
    ) {}

    public function handle(UploadBookCoverCommand $command): UploadBookCoverResult
    {
        $book = $this->books->findById($command->bookId);

        if ( ! $book) {
            throw new BookNotFoundException($command->bookId);
        }

        if ($book->coverPath) {
            $this->storage->delete($book->coverPath);
        }

        $path = $this->storage->upload(
            bookId: $command->bookId,
            tempPath: $command->tempPath,
            filename: $command->filename,
        );

        $updated = new Book(
            title: $book->title,
            slug: $book->slug,
            language: $book->language,
            edition: $book->edition,
            pagesCount: $book->pagesCount,
            accessType: $book->accessType,
            price: $book->price,
            status: $book->status,
            description: $book->description,
            isbn: $book->isbn,
            publishedAt: $book->publishedAt,
            coverPath: $path,
            publisher: $book->publisher,
            publishedYear: $book->publishedYear,
            id: $book->id,
        );

        $saved = $this->books->save($updated);

        return new UploadBookCoverResult(book: $saved);
    }
}
