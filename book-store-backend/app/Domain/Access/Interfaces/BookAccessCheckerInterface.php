<?php

declare(strict_types=1);

namespace App\Domain\Access\Interfaces;

interface BookAccessCheckerInterface
{
    public function canRead(int $userId, int $bookId): bool;
}
