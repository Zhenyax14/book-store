<?php

declare(strict_types=1);

namespace App\Application\User\UseCases\GetReader;

use App\Domain\User\Interfaces\ReaderRepositoryInterface;

final readonly class GetReaderHandler
{
    public function __construct(private ReaderRepositoryInterface $readerRepository) {}

    public function handle(GetReaderCommand $command): GetReaderResult
    {
        return new GetReaderResult($this->readerRepository->findOne($command->userId));
    }
}
