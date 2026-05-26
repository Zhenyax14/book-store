<?php

declare(strict_types=1);

namespace App\Domain\User\Interfaces;

use App\Domain\User\Entities\Reader;
use App\Domain\User\ValueObjects\ReaderFilter;

interface ReaderRepositoryInterface
{
    public function findAll(ReaderFilter $filter);

    public function findOne(int $userId): Reader;
}
