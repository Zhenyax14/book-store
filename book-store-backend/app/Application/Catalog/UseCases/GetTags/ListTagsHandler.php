<?php

namespace App\Application\Catalog\UseCases\GetTags;

use App\Domain\Catalog\Interfaces\TagRepositoryInterface;
use App\Domain\Catalog\ValueObjects\TagFilter;

final readonly class ListTagsHandler
{
    public function __construct(
        private TagRepositoryInterface $tags,
    ) {}

    public function handle(ListTagsCommand $command): ListTagsResult
    {
        $filter = new TagFilter(
            perPage: $command->perPage,
            page: $command->page,
        );

        $collection = $this->tags->findAll($filter);

        return new ListTagsResult(collection: $collection);
    }
}
