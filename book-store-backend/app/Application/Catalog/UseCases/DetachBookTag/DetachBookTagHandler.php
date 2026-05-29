<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\DetachBookTag;

use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Domain\Catalog\Interfaces\BookTagRepositoryInterface;

final readonly class DetachBookTagHandler
{
    public function __construct(
        private BookRepositoryInterface    $books,
        private BookTagRepositoryInterface $bookTags,
    ) {}

    public function handle(DetachBookTagCommand $command): void
    {
        $book = $this->books->findById($command->bookId);

        if ( ! $book) {
            throw new BookNotFoundException($command->bookId);
        }

        $this->bookTags->detach($command->bookId, $command->tagId);
    }
}
