<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\SyncBookTags;

use App\Domain\Catalog\Entities\Tag;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Exceptions\TagNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Domain\Catalog\Interfaces\BookTagRepositoryInterface;
use App\Domain\Catalog\Interfaces\TagRepositoryInterface;

final readonly class SyncBookTagsHandler
{
    public function __construct(
        private BookRepositoryInterface    $books,
        private TagRepositoryInterface     $tags,
        private BookTagRepositoryInterface $bookTags,
    ) {}

    public function handle(SyncBookTagsCommand $command): void
    {
        $book = $this->books->findById($command->bookId);

        if ( ! $book) {
            throw new BookNotFoundException($command->bookId);
        }

        $existingTags = $this->tags->findByIds($command->tagIds);

        if (count($existingTags) !== count($command->tagIds)) {
            $existingIds = array_map(static fn(Tag $t) => $t->id, $existingTags);
            $missing     = array_diff($command->tagIds, $existingIds);

            throw new TagNotFoundException(array_values($missing));
        }

        $this->bookTags->sync($command->bookId, $command->tagIds);
    }
}
