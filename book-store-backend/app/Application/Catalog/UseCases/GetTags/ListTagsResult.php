<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\GetTags;

use App\Domain\Catalog\ValueObjects\TagCollection;

final readonly class ListTagsResult
{
    public function __construct(
        public TagCollection $collection,
    ) {}
}
