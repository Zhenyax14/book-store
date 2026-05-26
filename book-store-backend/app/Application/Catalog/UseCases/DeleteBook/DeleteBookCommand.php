<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\DeleteBook;

final readonly class DeleteBookCommand
{
    public function __construct(
        public int $id,
    ) {}
}
