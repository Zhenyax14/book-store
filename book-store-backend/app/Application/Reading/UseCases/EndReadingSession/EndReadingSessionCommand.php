<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\EndReadingSession;

final readonly class EndReadingSessionCommand
{
    public function __construct(
        public int $sessionId,
        public int $endPageId,
        public int $durationSeconds,
    ) {}
}
