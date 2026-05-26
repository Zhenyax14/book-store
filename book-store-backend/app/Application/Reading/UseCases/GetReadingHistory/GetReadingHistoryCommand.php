<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingHistory;

final readonly class GetReadingHistoryCommand
{
    public function __construct(
        public int $userId,
    ) {}
}
