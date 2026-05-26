<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\DetachBookTag;

final readonly class DetachBookTagCommand
{
    public function __construct(
        public int $bookId,
        public int $tagId,
    ) {}
}
