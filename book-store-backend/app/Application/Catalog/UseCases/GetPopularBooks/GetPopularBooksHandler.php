<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\GetPopularBooks;

use App\Domain\Catalog\Interfaces\BookPopularityRepositoryInterface;

final readonly class GetPopularBooksHandler
{
    public function __construct(
        private BookPopularityRepositoryInterface $popularity,
    ) {}

    public function handle(GetPopularBooksCommand $command): GetPopularBooksResult
    {
        $collection = $this->popularity->findPopular(
            period: $command->period,
            perPage: $command->perPage,
            page: $command->page,
        );

        return new GetPopularBooksResult(collection: $collection);
    }
}
