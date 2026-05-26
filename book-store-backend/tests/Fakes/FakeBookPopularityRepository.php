<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Catalog\Enums\PopularityPeriodEnum;
use App\Domain\Catalog\Interfaces\BookPopularityRepositoryInterface;
use App\Domain\Catalog\ValueObjects\BookCollection;
use PHPUnit\Framework\Assert;

final class FakeBookPopularityRepository implements BookPopularityRepositoryInterface
{
    private ?PopularityPeriodEnum $calledPeriod = null;

    private ?int $calledPerPage = null;

    private ?int $calledPage = null;

    public function findPopular(PopularityPeriodEnum $period, int $perPage, int $page): BookCollection
    {
        $this->calledPeriod = $period;
        $this->calledPerPage = $perPage;
        $this->calledPage = $page;

        return new BookCollection(
            items: [],
            total: 0,
            perPage: $perPage,
            currentPage: $page,
        );
    }

    public function assertCalledWith(PopularityPeriodEnum $period, int $perPage, int $page): void
    {
        Assert::assertEquals($period, $this->calledPeriod, 'Period mismatch');
        Assert::assertEquals($perPage, $this->calledPerPage, 'PerPage mismatch');
        Assert::assertEquals($page, $this->calledPage, 'Page mismatch');
    }
}
