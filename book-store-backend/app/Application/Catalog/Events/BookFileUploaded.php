<?php

namespace App\Application\Catalog\Events;

final readonly class BookFileUploaded
{
    public function __construct(
        public int    $bookId,
        public string $filePath,
        public string $mimeType,
    ) {}
}
