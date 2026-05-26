<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\UploadBookFile;

final readonly class UploadBookFileCommand
{
    public function __construct(
        public int    $bookId,
        public string $tempPath,
        public string $filename,
        public string $mimeType,
    ) {}
}
