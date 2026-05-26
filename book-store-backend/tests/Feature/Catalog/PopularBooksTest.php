<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Fakes\FakeBookCoverStorage;
use Tests\TestCase;

final class PopularBooksTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instance(BookCoverStorageInterface::class, new FakeBookCoverStorage());
    }

    public function test_returns_200(): void
    {
        $this->getJson(route('books.popular'))
            ->assertOk();
    }

    public function test_response_structure_matches_book_collection(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->published()->create();
        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        $this->insertSessions($user->id, $book->id, count: 1, daysAgo: 1);

        $this->getJson(route('books.popular'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'title', 'slug', 'language',
                    'cover_url', 'access_type', 'status',
                    'price' => ['amount', 'currency', 'formatted'],
                ]],
                'meta' => ['total', 'per_page', 'current_page', 'total_pages'],
            ]);
    }

    public function test_returns_popular_books_sorted_by_session_count(): void
    {
        /** @var BookModel $popular */
        $popular = BookModel::factory()->published()->create(['title' => 'Popular Book']);
        /** @var BookModel $weak */
        $weak    = BookModel::factory()->published()->create(['title' => 'Weak Book']);
        /** @var UserModel $user */
        $user    = UserModel::factory()->create();

        $this->insertSessions($user->id, $popular->id, count: 5, daysAgo: 3);
        $this->insertSessions($user->id, $weak->id, count: 1, daysAgo: 3);

        $response = $this->getJson(route('books.popular', ['period' => 'week']));

        $response->assertOk();
        $this->assertEquals('Popular Book', $response->json('data.0.title'));
        $this->assertEquals('Weak Book', $response->json('data.1.title'));
    }

    public function test_excludes_sessions_outside_period(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->published()->create();
        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        $this->insertSessions($user->id, $book->id, count: 10, daysAgo: 60);

        $this->getJson(route('books.popular', ['period' => 'month']))
            ->assertOk()
            ->assertJsonFragment(['total' => 0]);
    }

    public function test_excludes_draft_books(): void
    {
        /** @var BookModel $draft */
        $draft = BookModel::factory()->create(['status' => 'draft']);
        /** @var UserModel $user */
        $user  = UserModel::factory()->create();

        $this->insertSessions($user->id, $draft->id, count: 99, daysAgo: 1);

        $this->getJson(route('books.popular', ['period' => 'week']))
            ->assertOk()
            ->assertJsonFragment(['total' => 0]);
    }

    public function test_validates_period_param(): void
    {
        $this->getJson(route('books.popular', ['period' => 'invalid']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['period']);
    }

    public function test_cover_url_uses_fake_storage(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->published()->create([
            'cover_path' => 'covers/1/cover.jpg',
        ]);
        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        $this->insertSessions($user->id, $book->id, count: 1, daysAgo: 1);

        $this->getJson(route('books.popular'))
            ->assertOk()
            ->assertJsonFragment([
                'cover_url' => 'https://fake-storage.test/covers/1/cover.jpg',
            ]);
    }

    private function insertSessions(int $userId, int $bookId, int $count, int $daysAgo): void
    {
        $rows = array_fill(0, $count, [
            'user_id'          => $userId,
            'book_id'          => $bookId,
            'started_at'       => now()->subDays($daysAgo),
            'ended_at'         => now()->subDays($daysAgo)->addHour(),
            'pages_read'       => 5,
            'duration_seconds' => 300,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        DB::table('reading_sessions')->insert($rows);
    }
}
