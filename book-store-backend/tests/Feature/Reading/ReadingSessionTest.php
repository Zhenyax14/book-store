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

final class ReadingSessionTest extends TestCase
{
    use DatabaseTransactions;

    private UserModel $user;

    private string    $token;

    private BookModel $book;

    private BookPageModel $page;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instance(
            ReadingProgressCacheRepositoryInterface::class,
            new FakeReadingProgressCacheRepository(),
        );

        $this->user = UserModel::factory()->create();
        $this->token = $this->user->createToken('reader-token')->plainTextToken;
        $this->book = BookModel::factory()->create([
            'access_type' => AccessTypeEnum::FREE,
        ]);
        /** @var BookChapterModel $chapter */
        $chapter = BookChapterModel::factory()
            ->for($this->book, 'book')
            ->create();
        $this->page = BookPageModel::factory()
            ->for($chapter, 'chapter')
            ->create();
    }

    // ── Start ─────────────────────────────────────────────────────

    public function test_start_session_returns_201(): void
    {
        $this->withToken($this->token)
            ->postJson(route('reading.session.start', ['bookId' => $this->book->id]), [])
            ->assertStatus(201)
            ->assertJsonStructure(['session_id', 'is_resumed']);
    }

    public function test_start_session_persists_to_database(): void
    {
        $this->withToken($this->token)
            ->postJson(route('reading.session.start', ['bookId' => $this->book->id]), []);

        $this->assertDatabaseHas('reading_sessions', [
            'user_id'  => $this->user->id,
            'book_id'  => $this->book->id,
            'ended_at' => null,
        ]);
    }

    public function test_start_session_is_idempotent(): void
    {
        $first = $this->withToken($this->token)
            ->postJson(route('reading.session.start', ['bookId' => $this->book->id]), []);

        $second = $this->withToken($this->token)
            ->postJson(route('reading.session.start', ['bookId' => $this->book->id]), []);

        $second->assertStatus(200)
            ->assertJsonFragment(['is_resumed' => true]);

        $this->assertEquals(
            $first->json('session_id'),
            $second->json('session_id'),
        );

        $this->assertDatabaseCount('reading_sessions', 1);
    }

    public function test_start_session_requires_auth(): void
    {
        $this->postJson(route('reading.session.start', ['bookId' => $this->book->id]), [])
            ->assertStatus(401);
    }

    // ── End ───────────────────────────────────────────────────────

    public function test_end_session_returns_200(): void
    {
        $started = $this->withToken($this->token)
            ->postJson(route('reading.session.start', ['bookId' => $this->book->id]), []);

        $this->withToken($this->token)
            ->patchJson(route('reading.session.end', [
                'bookId'    => $this->book->id,
                'sessionId' => $started->json('session_id'),
            ]), [
                'end_page_id'      => $this->page->id,
                'duration_seconds' => 900,
            ])
            ->assertStatus(200)
            ->assertJsonStructure(['pages_read', 'duration_seconds']);
    }

    public function test_end_session_persists_ended_at(): void
    {
        $started = $this->withToken($this->token)
            ->postJson(route('reading.session.start', ['bookId' => $this->book->id]), []);

        $sessionId = $started->json('session_id');

        $this->withToken($this->token)
            ->patchJson(route('reading.session.end', [
                'bookId'    => $this->book->id,
                'sessionId' => $sessionId,
            ]), [
                'end_page_id'      => $this->page->id,
                'duration_seconds' => 600,
            ]);

        $this->assertDatabaseMissing('reading_sessions', [
            'id'       => $sessionId,
            'ended_at' => null,
        ]);
    }

    public function test_end_session_validates_required_fields(): void
    {
        $this->withToken($this->token)
            ->patchJson(route('reading.session.end', [
                'bookId'    => $this->book->id,
                'sessionId' => 1,
            ]), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['end_page_id', 'duration_seconds']);
    }
}
