<?php

namespace App\Infrastructure\Parser;

use App\Application\Catalog\Interfaces\BookFileParserInterface;
use App\Application\Catalog\DTOs\ParsedBook;
use Smalot\PdfParser\Exception\MissingCatalogException;

final readonly class BookFileParserRouter implements BookFileParserInterface
{
    public function __construct(
        private PdfBookFileParser  $pdfParser,
        private EpubBookFileParser $epubParser,
    ) {}

    /**
     * @throws MissingCatalogException
     */
    public function parse(int $bookId, string $filePath, string $mimeType): ParsedBook
    {
        return match ($mimeType) {
            'application/pdf'      => $this->pdfParser->parse($bookId, $filePath),
            'application/epub+zip' => $this->epubParser->parse($bookId, $filePath),
            default => throw new \InvalidArgumentException(
                "Invalid format: {$mimeType}",
            ),
        };
    }
}
