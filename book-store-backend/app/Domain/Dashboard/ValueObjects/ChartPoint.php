<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\ValueObjects;

final readonly class ChartPoint
{
    public function __construct(
        public string $date,
        public int    $sessions,
        public int    $pagesRead,
        public int    $durationSeconds,
    ) {}
}
