<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\EndReadingSession;

final readonly class EndReadingSessionResult
{
    public function __construct(
        public int $pagesRead,
        public int $durationSeconds,
    ) {}
}
