<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingList;

use App\Domain\Reading\ValueObjects\ReadingEntryCollection;

final readonly class GetReadingListResult
{
    public function __construct(
        public ReadingEntryCollection $collection,
    ) {}
}
