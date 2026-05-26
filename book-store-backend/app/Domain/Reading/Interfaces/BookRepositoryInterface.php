<?php

declare(strict_types=1);

namespace App\Domain\Reading\Interfaces;

use App\Domain\Reading\Entities\Book;

interface BookRepositoryInterface
{
    public function findForReadingById(int $bookId, int $userId): ?Book;
}
