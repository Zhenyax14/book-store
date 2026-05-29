<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\PublishBook;

use App\Application\Shared\Interfaces\EventDispatcherInterface;
use App\Domain\Catalog\Events\BookPublished;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;

final readonly class PublishBookHandler
{
    public function __construct(
        private BookRepositoryInterface  $books,
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function handle(PublishBookCommand $command): PublishBookResult
    {
        $book = $this->books->findById($command->id)
            ?? throw new BookNotFoundException($command->id);

        $published = $book->publish();
        $saved = $this->books->save($published);

        $this->dispatcher->dispatch(
            new BookPublished(
                bookId: $saved->id,
                bookTitle: $saved->title,
                accessType: $saved->accessType->value,
            ),
        );

        return new PublishBookResult(book: $saved);
    }
}
