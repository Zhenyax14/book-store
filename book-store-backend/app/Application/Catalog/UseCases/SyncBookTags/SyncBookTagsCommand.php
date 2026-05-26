<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\SyncBookTags;

final readonly class SyncBookTagsCommand
{
    public function __construct(
        public readonly int   $bookId,
        /** @var int[] */
        public readonly array $tagIds,
    ) {}
}
