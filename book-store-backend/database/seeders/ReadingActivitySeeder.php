<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalog\Enums\BookStatusEnum;
use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\BookPageModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Random\RandomException;

final class ReadingActivitySeeder extends Seeder
{
    /**
     * How many readers get reading list entries (fraction of all readers).
     * 0.7 = 70% of readers are active.
     */
    private const float ACTIVE_READER_FRACTION = 0.7;

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $readers = UserModel::query()
            ->where('role', RoleEnum::READER)
            ->get();

        $publishedBooks = BookModel::query()
            ->where('status', BookStatusEnum::PUBLISHED)
            ->with('chapters.pages')
            ->get();

        if ($publishedBooks->isEmpty() || $readers->isEmpty()) {
            $this->command->warn('No readers or published books found — skipping ReadingActivitySeeder.');

            return;
        }

        $activityCount   = 0;
        $sessionCount    = 0;
        $progressCount   = 0;

        foreach ($readers as $reader) {
            if (random_int(1, 100) > self::ACTIVE_READER_FRACTION * 100) {
                continue;
            }

            // Each active reader picks 1–4 books
            $booksForReader = $publishedBooks->random(min(random_int(1, 4), $publishedBooks->count()));

            foreach ($booksForReader as $book) {
                $status = $this->randomStatus();

                $this->upsertReadingListEntry($reader->id, $book->id, $status, $book->pages_count);
                $activityCount++;

                if (in_array($status, [ReadingStatusEnum::READING, ReadingStatusEnum::FINISHED], true)) {
                    $sessionCount += $this->seedReadingSessions($reader->id, $book, $status);
                    $progressCount += $this->seedReadingProgress($reader->id, $book, $status);
                }
            }
        }

        $this->command->info("Reading list entries: {$activityCount}");
        $this->command->info("Reading sessions:     {$sessionCount}");
        $this->command->info("Progress records:     {$progressCount}");
    }

    /**
     * @throws RandomException
     */
    private function upsertReadingListEntry(
        int $userId,
        int $bookId,
        ReadingStatusEnum $status,
        int $totalPages,
    ): void {
        $currentPage = match ($status) {
            ReadingStatusEnum::WANT_TO_READ => 0,
            ReadingStatusEnum::READING      => random_int(1, max(1, (int) ($totalPages * 0.8))),
            ReadingStatusEnum::FINISHED     => $totalPages,
            ReadingStatusEnum::DROPPED      => random_int(1, max(1, (int) ($totalPages * 0.3))),
        };

        $startedAt  = in_array($status, [ReadingStatusEnum::READING, ReadingStatusEnum::FINISHED, ReadingStatusEnum::DROPPED], true)
            ? now()->subDays(random_int(5, 60))
            : null;

        $finishedAt = ReadingStatusEnum::FINISHED === $status
            ? now()->subDays(random_int(1, 30))
            : null;

        DB::table('user_reading_list')->upsert(
            [
                'user_id'      => $userId,
                'book_id'      => $bookId,
                'status'       => $status->value,
                'current_page' => $currentPage,
                'total_pages'  => $totalPages > 0 ? $totalPages : null,
                'started_at'   => $startedAt,
                'finished_at'  => $finishedAt,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            ['user_id', 'book_id'],
            ['status', 'current_page', 'started_at', 'finished_at', 'updated_at'],
        );
    }

    /**
     * @throws RandomException
     */
    private function seedReadingSessions(int $userId, BookModel $book, ReadingStatusEnum $status): int
    {
        $sessionsToCreate = random_int(1, 5);

        $rows = [];
        for ($i = 0; $i < $sessionsToCreate; $i++) {
            $startedAt = now()->subDays(random_int(1, 45))->subHours(random_int(0, 6));

            $rows[] = [
                'user_id'          => $userId,
                'book_id'          => $book->id,
                'started_at'       => $startedAt,
                'ended_at'         => $startedAt->addMinutes(random_int(10, 120)),
                'pages_read'       => random_int(3, 20),
                'duration_seconds' => random_int(600, 7200),
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        DB::table('reading_sessions')->insert($rows);

        return $sessionsToCreate;
    }

    /**
     * @throws RandomException
     */
    private function seedReadingProgress(int $userId, BookModel $book, ReadingStatusEnum $status): int
    {
        $pages      = $book->chapters->flatMap(fn($c) => $c->pages);
        $totalPages = $book->pages_count > 0 ? $book->pages_count : $pages->count();

        if (0 === $totalPages || $pages->isEmpty()) {
            return 0;
        }

        $globalPageNumber = ReadingStatusEnum::FINISHED === $status
            ? $totalPages
            : random_int(1, max(1, (int) ($totalPages * 0.8)));

        /** @var BookPageModel|null $page */
        $page = $pages->first(fn($p) => $p->global_number === $globalPageNumber)
            ?? $pages->last();

        if (null === $page) {
            return 0;
        }

        $completionPct = $totalPages > 0
            ? round(($globalPageNumber / $totalPages) * 100, 2)
            : 0.0;

        $isFinished = $completionPct >= 100.0;

        DB::table('user_reading_progress')->upsert(
            [
                'user_id'               => $userId,
                'book_id'               => $book->id,
                'chapter_id'            => $page->chapter_id,
                'page_id'               => $page->id,
                'global_page_number'    => $globalPageNumber,
                'scroll_position'       => random_int(0, 100),
                'completion_percentage' => $completionPct,
                'is_finished'           => $isFinished,
                'last_read_at'          => now()->subDays(random_int(0, 10)),
                'finished_at'           => $isFinished ? now()->subDays(random_int(0, 5)) : null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            ['user_id', 'book_id'],
            ['chapter_id', 'page_id', 'global_page_number', 'scroll_position',
                'completion_percentage', 'is_finished', 'last_read_at', 'finished_at', 'updated_at'],
        );

        return 1;
    }

    /**
     * @throws RandomException
     */
    private function randomStatus(): ReadingStatusEnum
    {
        $weights = [
            ReadingStatusEnum::WANT_TO_READ->value => 30,
            ReadingStatusEnum::READING->value      => 35,
            ReadingStatusEnum::FINISHED->value     => 25,
            ReadingStatusEnum::DROPPED->value      => 10,
        ];

        $roll  = random_int(1, 100);
        $cumulative = 0;

        foreach ($weights as $statusValue => $weight) {
            $cumulative += $weight;
            if ($roll <= $cumulative) {
                return ReadingStatusEnum::from($statusValue);
            }
        }

        return ReadingStatusEnum::WANT_TO_READ;
    }
}
