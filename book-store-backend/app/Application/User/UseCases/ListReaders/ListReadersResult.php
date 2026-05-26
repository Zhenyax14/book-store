<?php

declare(strict_types=1);

namespace App\Application\User\UseCases\ListReaders;

use App\Domain\User\ValueObjects\ReaderCollection;

final readonly class ListReadersResult
{
    public function __construct(
        public ReaderCollection $collection,
    ) {}
}
