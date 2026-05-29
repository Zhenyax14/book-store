<?php

namespace App\Application\Catalog\Interfaces;

use App\Application\Catalog\DTOs\ParsedBook;

interface BookFileParserInterface
{
    public function parse(int $bookId, string $filePath, string $mimeType): ParsedBook;
}
