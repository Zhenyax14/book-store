<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\AddBookToList;

use App\Domain\Reading\Entities\ReadingEntry;

final readonly class AddBookToListResult
{
    public function __construct(
        public ReadingEntry $entry,
    ) {}
}
