<?php

namespace Tests\Unit\Application\Catalog\UseCases;

use App\Application\Catalog\UseCases\GetPopularBooks\GetPopularBooksHandler;
use App\Application\Catalog\UseCases\GetPopularBooks\GetPopularBooksCommand;
use App\Domain\Catalog\Enums\PopularityPeriodEnum;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeBookPopularityRepository;

final class GetPopularBooksTest extends TestCase
{
    private FakeBookPopularityRepository $repo;

    private GetPopularBooksHandler       $handler;

    protected function setUp(): void
    {
        $this->repo    = new FakeBookPopularityRepository();
        $this->handler = new GetPopularBooksHandler($this->repo);
    }

    public function test_delegates_to_repository_with_correct_params(): void
    {
        $this->handler->handle(new GetPopularBooksCommand(
            period: PopularityPeriodEnum::WEEK,
            perPage: 10,
            page: 2,
        ));

        $this->repo->assertCalledWith(PopularityPeriodEnum::WEEK, 10, 2);
    }

    public function test_returns_result_with_collection(): void
    {
        $result = $this->handler->handle(new GetPopularBooksCommand(
            period: PopularityPeriodEnum::MONTH,
        ));

        $this->assertSame(0, $result->collection->total);
    }
}
