<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\UploadBookFile;

final readonly class UploadBookFileResult
{
    public function __construct(
        public int    $bookId,
        public string $filePath,
    ) {}
}
