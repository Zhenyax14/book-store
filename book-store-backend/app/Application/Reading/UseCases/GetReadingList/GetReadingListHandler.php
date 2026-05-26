<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingList;

use App\Domain\Reading\Interfaces\ReadingListRepositoryInterface;

final readonly class GetReadingListHandler
{
    public function __construct(
        private ReadingListRepositoryInterface $entries,
    ) {}

    public function handle(GetReadingListCommand $command): GetReadingListResult
    {
        $collection = $this->entries->findByUser(
            userId: $command->userId,
            status: $command->status,
            perPage: $command->perPage,
            page: $command->page,
        );

        return new GetReadingListResult(collection: $collection);
    }
}
