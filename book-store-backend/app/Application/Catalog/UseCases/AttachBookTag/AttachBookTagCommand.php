<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\AttachBookTag;

final readonly class AttachBookTagCommand
{
    public function __construct(
        public int $bookId,
        public int $tagId,
    ) {}
}
