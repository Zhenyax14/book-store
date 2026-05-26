<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\AttachBookTag;

use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Exceptions\TagNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Domain\Catalog\Interfaces\BookTagRepositoryInterface;
use App\Domain\Catalog\Interfaces\TagRepositoryInterface;

final readonly class AttachBookTagHandler
{
    public function __construct(
        private BookRepositoryInterface    $books,
        private TagRepositoryInterface     $tags,
        private BookTagRepositoryInterface $bookTags,
    ) {}

    public function handle(AttachBookTagCommand $command): void
    {
        $book = $this->books->findById($command->bookId);

        if ( ! $book) {
            throw new BookNotFoundException($command->bookId);
        }

        $tag = $this->tags->findById($command->tagId);

        if ( ! $tag) {
            throw new TagNotFoundException([$command->tagId]);
        }

        $this->bookTags->attach($command->bookId, $command->tagId);
    }
}
