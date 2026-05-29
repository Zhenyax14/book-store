<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Interfaces;

use App\Domain\Catalog\Enums\PopularityPeriodEnum;
use App\Domain\Catalog\ValueObjects\BookCollection;

interface BookPopularityRepositoryInterface
{
    public function findPopular(PopularityPeriodEnum $period, int $perPage, int $page): BookCollection;
}
