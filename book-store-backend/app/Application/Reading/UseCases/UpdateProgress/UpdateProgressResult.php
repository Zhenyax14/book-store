<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\UpdateProgress;

use App\Domain\Reading\Entities\ReadingEntry;

final readonly class UpdateProgressResult
{
    public function __construct(
        public ReadingEntry $entry,
    ) {}
}
