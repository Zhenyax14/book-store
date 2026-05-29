<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\UpdateBook;

use App\Application\Catalog\Interfaces\BookSearchIndexInterface;
use App\Domain\Catalog\Entities\Book;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;

final readonly class UpdateBookHandler
{
    public function __construct(
        private BookRepositoryInterface  $books,
        private BookSearchIndexInterface $searchIndex,
    ) {}

    public function handle(UpdateBookCommand $command): UpdateBookResult
    {
        $book = $this->books->findById($command->id);

        if ( ! $book) {
            throw new BookNotFoundException($command->id);
        }

        $updated = new Book(
            title: $command->title,
            slug: $book->slug,
            language: $command->language,
            edition: $book->edition,
            pagesCount: $book->pagesCount,
            accessType: $command->accessType,
            price: new Money($command->price, new Currency($command->currency)),
            status: $book->status,
            description: $command->description,
            isbn: $command->isbn,
            publishedAt: $book->publishedAt,
            coverPath: $book->coverPath,
            filePath: $book->filePath,
            publisher: $command->publisher,
            publishedYear: $command->publishedYear,
            id: $book->id,
        );

        $saved = $this->books->save($updated);

        if ($saved->isPublished()) {
            $this->searchIndex->index($saved);
        }

        return new UpdateBookResult(book: $saved);
    }
}
