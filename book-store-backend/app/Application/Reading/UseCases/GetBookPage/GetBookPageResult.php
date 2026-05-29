<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetBookPage;

use App\Domain\Reading\Entities\BookPage;
use App\Domain\Reading\ValueObjects\AdjacentPages;
use App\Domain\Reading\ValueObjects\ReadingProgress;

final readonly class GetBookPageResult
{
    public function __construct(
        public BookPage        $page,
        public AdjacentPages   $adjacent,
        public ReadingProgress $progress,
    ) {}
}
