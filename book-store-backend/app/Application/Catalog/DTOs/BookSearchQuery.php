<?php

declare(strict_types=1);

namespace App\Application\Catalog\DTOs;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;

final readonly class BookSearchQuery
{
    public function __construct(
        public string          $query,
        public ?BookStatusEnum $status     = null,
        public ?AccessTypeEnum $accessType = null,
        public ?string         $language   = null,
        public int             $limit      = 20,
        public int             $offset     = 0,
    ) {}
}
