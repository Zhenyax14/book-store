<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingProgress;

use App\Domain\Reading\ValueObjects\ReadingPosition;
use App\Domain\Reading\ValueObjects\ReadingProgress;
use DateTimeImmutable;

final readonly class GetReadingProgressResult
{
    public function __construct(
        public ReadingProgress    $progress,
        public ?ReadingPosition   $lastPosition,
        public bool               $isFinished,
        public ?DateTimeImmutable $lastReadAt,
    ) {}
}
