<?php

declare(strict_types=1);

namespace App\Application\User\UseCases\GetReader;

final readonly class GetReaderCommand
{
    public function __construct(
        public int $userId,
    ) {}
}
