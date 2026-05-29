<?php

namespace App\Application\Catalog\Jobs;

use App\Application\Catalog\Interfaces\BookFileParserInterface;
use App\Application\Shared\Interfaces\SlugGeneratorInterface;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Domain\Reading\Entities\BookChapter;
use App\Domain\Reading\Entities\BookPage;
use App\Domain\Reading\Interfaces\BookChapterRepositoryInterface;
use App\Domain\Reading\Interfaces\BookPageRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ParseBookFileJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries   = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly int    $bookId,
        public readonly string $filePath,
        public readonly string $mimeType,
    ) {}

    public function uniqueId(): string
    {
        return (string) $this->bookId;
    }

    /**
     * @throws Throwable
     */
    public function handle(
        BookFileParserInterface        $parser,
        BookRepositoryInterface        $books,
        BookChapterRepositoryInterface $chapters,
        BookPageRepositoryInterface    $pages,
        SlugGeneratorInterface         $slugger,
    ): void {
        $parsed = $parser->parse($this->bookId, $this->filePath, $this->mimeType);

        DB::beginTransaction();

        try {
            $pages->deleteByBookId($this->bookId);
            $chapters->deleteByBookId($this->bookId);

            $globalPageNumber = 1;

            foreach ($parsed->chapters as $parsedChapter) {
                $chapter = $chapters->save(
                    new BookChapter(
                        id: null,
                        bookId: $parsed->bookId,
                        volumeId: null,
                        number: $parsedChapter->number,
                        title: $parsedChapter->title,
                        slug: $slugger->generate($parsedChapter->title),
                        readingTimeMinutes: (int) ceil(count($parsedChapter->pages) * 300 / 200),
                        isPublished: false,
                    ),
                );

                foreach ($parsedChapter->pages as $parsedPage) {
                    $pages->save(new BookPage(
                        id: null,
                        chapterId: $chapter->id,
                        number: $parsedPage->number,
                        globalNumber: $globalPageNumber++,
                        content: $parsedPage->content,
                        contentFormat: $parsedPage->contentFormat,
                        wordCount: $parsedPage->wordCount,
                    ));
                }
            }

            $book = $books->findById($this->bookId);
            $books->save(
                $book->withPagesCount($parsed->totalPages)
                    ->withFilePath($this->filePath),
            );

            DB::commit();

        } catch (Throwable $e) {
            DB::rollBack();

            Log::warning('ParseBookFileJob failed', [
                'bookId'   => $this->bookId,
                'filePath' => $this->filePath,
                'attempt'  => $this->attempts(),
                'error'    => $e->getMessage(),
            ]);

            if ($this->attempts() >= $this->tries) {
                Log::error('ParseBookFileJob exhausted all retries', [
                    'bookId'   => $this->bookId,
                    'filePath' => $this->filePath,
                    'error'    => $e->getMessage(),
                ]);

                $this->fail($e);

                return;
            }

            $this->release($this->backoff);
        }
    }
}
