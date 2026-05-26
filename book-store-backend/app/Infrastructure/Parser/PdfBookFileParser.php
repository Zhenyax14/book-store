<?php

namespace App\Infrastructure\Parser;

use App\Application\Catalog\DTOs\ParsedBook;
use App\Application\Catalog\DTOs\ParsedPage;
use App\Application\Catalog\DTOs\ParsedChapter;
use App\Domain\Reading\Enums\ContentFormatEnum;
use Exception;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;

final class PdfBookFileParser
{
    /**
     * @throws Exception
     */
    public function parse(int $bookId, string $filePath): ParsedBook
    {
        $localPath = $this->downloadToLocal($filePath);

        try {
            $parser = new Parser();
            $pdf    = $parser->parseFile($localPath);
            $pages  = $pdf->getPages();

            $parsedPages = array_map(
                static fn(Page $page, int $index) => new ParsedPage(
                    number: $index + 1,
                    content: $page->getText(),
                    contentFormat: ContentFormatEnum::TEXT,
                    wordCount: str_word_count($page->getText()),
                ),
                $pages,
                array_keys($pages),
            );

            return new ParsedBook(
                bookId: $bookId,
                totalPages: count($parsedPages),
                chapters: [
                    new ParsedChapter(
                        title: 'Main chapter',
                        number: 1,
                        pages: $parsedPages,
                    ),
                ],
            );
        } finally {
            if (file_exists($localPath)) {
                unlink($localPath);
            }
        }

    }

    private function downloadToLocal(string $s3Path): string
    {
        $localPath = sys_get_temp_dir() . '/' . uniqid('pdf_', true) . '.pdf';
        $contents  = Storage::disk('s3')->get($s3Path);
        file_put_contents($localPath, $contents);

        return $localPath;
    }
}
