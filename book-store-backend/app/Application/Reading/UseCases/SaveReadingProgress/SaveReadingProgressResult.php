<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\SaveReadingProgress;

final readonly class SaveReadingProgressResult
{
    public function __construct(
        public float $completionPercentage,
        public bool  $isFinished,
    ) {}
}
