<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalog\Enums\BookStatusEnum;
use App\Domain\Reading\Enums\ContentFormatEnum;
use App\Infrastructure\Persistence\Models\BookChapterModel;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\BookPageModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Random\RandomException;

final class BookContentSeeder extends Seeder
{
    private const int CHAPTERS_PER_BOOK = 3;

    private const int PAGES_PER_CHAPTER = 5;

    private const int WORDS_PER_PAGE    = 300;

    /**
     * Only seed content for published books that have no chapters yet.
     * This keeps the seeder idempotent.
     * @throws RandomException
     */
    public function run(): void
    {
        $books = BookModel::query()
            ->where('status', BookStatusEnum::PUBLISHED)
            ->whereDoesntHave('chapters')
            ->get();

        $count = 0;

        foreach ($books as $book) {
            $this->seedBookContent($book);
            $count++;
        }

        $this->command->info("Book content seeded for {$count} books.");
    }

    /**
     * @throws RandomException
     */
    private function seedBookContent(BookModel $book): void
    {
        $globalPageNumber = 1;
        $totalPages       = 0;

        for ($chapterNumber = 1; $chapterNumber <= self::CHAPTERS_PER_BOOK; $chapterNumber++) {
            $title = "Chapter {$chapterNumber}: " . fake()->sentence(4, false);

            /** @var BookChapterModel $chapter */
            $chapter = BookChapterModel::query()->create([
                'book_id'               => $book->id,
                'number'                => $chapterNumber,
                'title'                 => $title,
                'slug'                  => Str::slug($title),
                'reading_time_minutes'  => (int) ceil(
                    self::PAGES_PER_CHAPTER * self::WORDS_PER_PAGE / 200,
                ),
                'is_published'          => true,
            ]);

            for ($pageNumber = 1; $pageNumber <= self::PAGES_PER_CHAPTER; $pageNumber++) {
                $content   = $this->generatePageContent($book->title, $chapterNumber, $pageNumber);
                $wordCount = str_word_count($content);

                BookPageModel::query()->create([
                    'chapter_id'     => $chapter->id,
                    'number'         => $pageNumber,
                    'global_number'  => $globalPageNumber,
                    'content'        => $content,
                    'content_format' => ContentFormatEnum::TEXT,
                    'word_count'     => $wordCount,
                ]);

                $globalPageNumber++;
                $totalPages++;
            }
        }

        // Update the book's page count
        $book->update(['pages_count' => $totalPages]);
    }

    /**
     * @throws RandomException
     */
    private function generatePageContent(string $bookTitle, int $chapter, int $page): string
    {
        $paragraphs = [];

        for ($i = 0; $i < 4; $i++) {
            $paragraphs[] = fake()->paragraph(random_int(8, 15));
        }

        return implode("\n\n", $paragraphs);
    }
}
