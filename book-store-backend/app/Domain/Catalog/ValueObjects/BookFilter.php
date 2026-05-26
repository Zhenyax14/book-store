<?php

declare(strict_types=1);

namespace App\Domain\Catalog\ValueObjects;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;

final readonly class BookFilter
{
    public function __construct(
        public ?BookStatusEnum $status = null,
        public ?AccessTypeEnum $accessType = null,
        public ?string         $language = null,
        public ?string         $search = null,
        public int             $perPage = 20,
        public int             $page = 1,
        public ?string $tag = null,
    ) {}
}
