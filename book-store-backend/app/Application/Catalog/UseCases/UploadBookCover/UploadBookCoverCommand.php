<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\UploadBookCover;

final readonly class UploadBookCoverCommand
{
    public function __construct(
        public int    $bookId,
        public string $tempPath,
        public string $filename,
        public string $mimeType,
    ) {}
}
