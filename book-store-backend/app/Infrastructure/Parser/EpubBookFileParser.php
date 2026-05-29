<?php

namespace App\Infrastructure\Parser;

use App\Application\Catalog\DTOs\ParsedBook;
use App\Application\Catalog\DTOs\ParsedChapter;
use App\Application\Catalog\DTOs\ParsedPage;
use App\Domain\Reading\Enums\ContentFormatEnum;
use Exception;
use Illuminate\Support\Facades\Storage;
use lywzx\epub\EpubParser;

final class EpubBookFileParser
{
    /**
     * @throws Exception
     */
    public function parse(int $bookId, string $filePath): ParsedBook
    {
        $localPath = $this->downloadToLocal($filePath);

        try {
            $epubParser = new EpubParser($localPath);
            $epubParser->parse();

            $toc = $epubParser->getTOC();

            $parsedChapters  = [];
            $chapterNumber   = 1;

            foreach ($toc as $chapterMeta) {
                $chapterId = $chapterMeta['id'];
                $title     = $chapterMeta['naam']
                    ?? "Chapter {$chapterNumber}";

                $content = $epubParser->getChapter($chapterId);

                $pages   = $this->splitIntoPages($content);

                if (empty($pages)) {
                    continue;
                }

                $parsedChapters[] = new ParsedChapter(
                    title: $title,
                    number: $chapterNumber++,
                    pages: $pages,
                );
            }

            $totalPages = array_sum(
                array_map(static fn(ParsedChapter $c) => count($c->pages), $parsedChapters),
            );

            return new ParsedBook(
                bookId: $bookId,
                totalPages: $totalPages,
                chapters: $parsedChapters,
            );
        } finally {
            if (file_exists($localPath)) {
                unlink($localPath);
            }
        }

    }

    private function splitIntoPages(string $content): array
    {
        $plainText = strip_tags($content);
        $words     = preg_split('/\s+/', mb_trim($plainText), flags: PREG_SPLIT_NO_EMPTY);

        if (empty($words)) {
            return [];
        }

        $chunks = array_chunk($words, 300);
        $pages  = [];

        foreach ($chunks as $index => $chunk) {
            $pages[] = new ParsedPage(
                number: $index + 1,
                content: implode(' ', $chunk),
                contentFormat: ContentFormatEnum::TEXT,
                wordCount: count($chunk),
            );
        }

        return $pages;
    }

    private function downloadToLocal(string $s3Path): string
    {
        $localPath = sys_get_temp_dir() . '/' . uniqid('epub_', true) . '.epub';
        $contents  = Storage::disk('s3')->get($s3Path);
        file_put_contents($localPath, $contents);

        return $localPath;
    }
}
