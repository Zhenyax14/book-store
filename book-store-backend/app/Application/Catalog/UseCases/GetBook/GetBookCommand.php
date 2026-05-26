<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\GetBook;

final readonly class GetBookCommand
{
    public function __construct(
        public int $id,
    ) {}
}
