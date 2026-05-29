<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetBook;

use App\Domain\Reading\Exceptions\BookNotFoundException;
use App\Domain\Reading\Interfaces\BookRepositoryInterface;

final readonly class GetBookHandler
{
    public function __construct(
        public BookRepositoryInterface $bookRepository,
    ) {}

    public function handle(GetBookCommand $command): GetBookResult
    {
        $result = $this->bookRepository->findForReadingById($command->bookId, $command->userId);

        if (null === $result) {
            throw new BookNotFoundException($command->bookId);
        }

        return new GetBookResult($result);
    }
}
