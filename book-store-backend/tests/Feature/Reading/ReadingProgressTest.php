<?php

declare(strict_types=1);

namespace Tests\Feature\Reading;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Reading\Interfaces\ReadingProgressCacheRepositoryInterface;
use App\Infrastructure\Persistence\Models\BookChapterModel;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\BookPageModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fakes\FakeReadingProgressCacheRepository;
use Tests\TestCase;

final class ReadingProgressTest extends TestCase
{
    use DatabaseTransactions;

    private UserModel $user;

    private string $token;

    private BookModel $book;

    private BookChapterModel $chapter;

    private BookPageModel $page;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserModel::factory()->create();
        $this->token = $this->user->createToken('reader-token')->plainTextToken;
        $this->book = BookModel::factory()->create([
            'pages_count' => 100,
            'access_type' => AccessTypeEnum::FREE,
        ]);
        $this->chapter = BookChapterModel::factory()
            ->for($this->book, 'book')
            ->create(['number' => 1,]);

        $this->page = BookPageModel::factory()
            ->for($this->chapter, 'chapter')
            ->create([
                'number' => 1,
                'global_number' => 1,
            ]);

        $this->instance(
            ReadingProgressCacheRepositoryInterface::class,
            new FakeReadingProgressCacheRepository(),
        );
    }

    public function test_get_progress_returns_200(): void
    {
        $this->withToken($this->token)
            ->getJson(route('reading.progress.show', ['bookId' => $this->book->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'progress' => ['book_id', 'total_pages', 'read_pages', 'percentage', 'is_finished'],
                'last_position',
                'last_read_at',
            ]);
    }

    public function test_get_progress_returns_zeros_for_new_reader(): void
    {
        $this->withToken($this->token)
            ->getJson(route('reading.progress.show', ['bookId' => $this->book->id]))
            ->assertStatus(200)
            ->assertJsonFragment(['percentage' => 0.0, 'is_finished' => false])
            ->assertJsonFragment(['last_position' => null]);
    }

    public function test_get_progress_requires_auth(): void
    {
        $this->getJson(route('reading.progress.show', ['bookId' => $this->book->id]))
            ->assertStatus(401);
    }

    public function test_save_progress_returns_200(): void
    {
        $this->withToken($this->token)
            ->postJson(route('reading.progress.save', ['bookId' => $this->book->id]), [
                'chapter_id' => $this->chapter->id,
                'page_id' => $this->page->id,
                'global_page_number' => $this->page->global_number,
                'scroll_position' => 40,
                'total_pages' => 100,
                'book_title' => $this->book->title,
            ])
            ->assertStatus(200)
            ->assertJsonStructure(['completion_percentage', 'is_finished']);
    }

    public function test_save_progress_persists_to_database(): void
    {
        $this->withToken($this->token)
            ->postJson(route('reading.progress.save', ['bookId' => $this->book->id]), [
                'chapter_id' => $this->chapter->id,
                'page_id' => $this->page->id,
                'global_page_number' => $this->page->global_number,
                'scroll_position' => 0,
                'total_pages' => 100,
                'book_title' => $this->book->title,
            ]);

        $this->assertDatabaseHas('user_reading_progress', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'page_id' => $this->page->id,
        ]);
    }

    public function test_save_progress_returns_correct_percentage(): void
    {
        /** @var BookPageModel $page50 */
        $page50 = BookPageModel::factory()->create([
            'chapter_id' => $this->chapter->id,
            'number' => 50,
            'global_number' => 50,
        ]);

        $this->withToken($this->token)
            ->postJson(route('reading.progress.save', ['bookId' => $this->book->id]), [
                'chapter_id' => $this->chapter->id,
                'page_id' => $page50->id,
                'global_page_number' => $page50->global_number,
                'scroll_position' => 0,
                'total_pages' => 100,
                'book_title' => $this->book->title,
            ])
            ->assertJsonFragment(['completion_percentage' => 50.0]);
    }

    public function test_save_progress_marks_finished_on_last_page(): void
    {
        /** @var BookPageModel $lastPage */
        $lastPage = BookPageModel::factory()
            ->for($this->chapter, 'chapter')
            ->create([
                'number' => 100,
                'global_number' => 100,
            ]);

        $this->withToken($this->token)
            ->postJson(route('reading.progress.save', ['bookId' => $this->book->id]), [
                'chapter_id' => $this->chapter->id,
                'page_id' => $lastPage->id,
                'global_page_number' => $lastPage->global_number,
                'scroll_position' => 100,
                'total_pages' => 100,
                'book_title' => $this->book->title,
            ])
            ->assertJsonFragment(['is_finished' => true]);

        $this->assertDatabaseHas('user_reading_progress', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'is_finished' => true,
        ]);
    }

    public function test_save_progress_validates_required_fields(): void
    {
        $this->withToken($this->token)
            ->postJson(route('reading.progress.save', ['bookId' => $this->book->id]), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['chapter_id', 'page_id', 'scroll_position', 'total_pages']);
    }

    public function test_save_progress_validates_scroll_position_range(): void
    {
        $this->withToken($this->token)
            ->postJson(route('reading.progress.save', ['bookId' => $this->book->id]), [
                'chapter_id' => 1,
                'page_id' => 1,
                'scroll_position' => 150,
                'total_pages' => 100,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['scroll_position']);
    }
}
