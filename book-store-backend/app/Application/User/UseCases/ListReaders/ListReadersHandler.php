<?php

declare(strict_types=1);

namespace App\Application\User\UseCases\ListReaders;

use App\Domain\User\Interfaces\ReaderRepositoryInterface;
use App\Domain\User\ValueObjects\ReaderFilter;

final readonly class ListReadersHandler
{
    public function __construct(private ReaderRepositoryInterface $readerRepository) {}

    public function handle(ListReadersCommand $command): ListReadersResult
    {
        $filter = new ReaderFilter(
            filter: $command->filter,
            search: $command->search,
            perPage: $command->perPage,
            page: $command->page,
        );

        return new ListReadersResult(
            $this->readerRepository->findAll($filter),
        );
    }
}
