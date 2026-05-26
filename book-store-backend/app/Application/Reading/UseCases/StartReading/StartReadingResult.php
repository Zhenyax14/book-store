<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\StartReading;

use App\Domain\Reading\Entities\ReadingEntry;

final readonly class StartReadingResult
{
    public function __construct(
        public ReadingEntry $entry,
    ) {}
}
