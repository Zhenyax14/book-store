<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\StartReadingSession;

final readonly class StartReadingSessionCommand
{
    public function __construct(
        public int  $userId,
        public int  $bookId,
        public ?int $currentPageId,
    ) {}
}
