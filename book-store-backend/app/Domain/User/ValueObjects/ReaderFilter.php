<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use App\Domain\User\Enums\ReaderFilterEnum;

final readonly class ReaderFilter
{
    public function __construct(
        public ?ReaderFilterEnum $filter = null,
        public ?string           $search = null,
        public int               $perPage = 20,
        public int               $page = 1,
    ) {}
}
