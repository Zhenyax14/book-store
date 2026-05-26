<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\StartReadingSession;

final readonly class StartReadingSessionResult
{
    public function __construct(
        public int  $sessionId,
        public bool $isResumed,
    ) {}
}
