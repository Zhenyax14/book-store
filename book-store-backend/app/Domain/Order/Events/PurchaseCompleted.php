<?php

declare(strict_types=1);

namespace App\Domain\Order\Events;

final readonly class PurchaseCompleted
{
    public function __construct(
        public int    $userId,
        public int    $bookId,
        public string $bookTitle,
    ) {}
}
