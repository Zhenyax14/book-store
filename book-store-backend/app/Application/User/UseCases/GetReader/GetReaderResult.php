<?php

declare(strict_types=1);

namespace App\Application\User\UseCases\GetReader;

use App\Domain\User\Entities\Reader;

final readonly class GetReaderResult
{
    public function __construct(
        public Reader $reader,
    ) {}
}
