<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\PublishBook;

final readonly class PublishBookCommand
{
    public function __construct(
        public int $id,
    ) {}
}
