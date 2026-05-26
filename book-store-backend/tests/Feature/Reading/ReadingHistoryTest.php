<?php

declare(strict_types=1);

namespace Tests\Feature\Reading;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Reading\Interfaces\ReadingProgressCacheRepositoryInterface;
use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\BookChapterModel;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\BookPageModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fakes\FakeReadingProgressCacheRepository;
use Tests\TestCase;

final class ReadingHistoryTest extends TestCase
{
    use DatabaseTransactions;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instance(
            ReadingProgressCacheRepositoryInterface::class,
            new FakeReadingProgressCacheRepository(),
        );

        $user = UserModel::factory()->create([
            'role' => RoleEnum::READER,
        ]);
        $this->token = $user->createToken('reader-token')->plainTextToken;
    }

    public function test_history_returns_200(): void
    {
        $this->withToken($this->token)
            ->getJson(route('reading.history'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['total_sessions', 'total_pages_read', 'total_duration_seconds'],
            ]);
    }

    public function test_history_is_empty_for_new_user(): void
    {
        $this->withToken($this->token)
            ->getJson(route('reading.history'))
            ->assertJsonFragment([
                'total_sessions'         => 0,
                'total_pages_read'       => 0,
                'total_duration_seconds' => 0,
            ]);
    }

    public function test_history_contains_completed_sessions(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create([
            'access_type' => AccessTypeEnum::FREE,
        ]);
        /** @var BookChapterModel $chapter */
        $chapter = BookChapterModel::factory()
            ->for($book, 'book')
            ->create();
        /** @var BookPageModel $page */
        $page = BookPageModel::factory()
            ->for($chapter, 'chapter')
            ->create();

        $started = $this->withToken($this->token)
            ->postJson(route('reading.session.start', ['bookId' => $book->id]), []);

        $this->withToken($this->token)
            ->patchJson(route('reading.session.end', [
                'bookId'    => $book->id,
                'sessionId' => $started->json('session_id'),
            ]), [
                'end_page_id'      => $page->id,
                'duration_seconds' => 300,
            ]);

        $this->withToken($this->token)
            ->getJson(route('reading.history'))
            ->assertJsonFragment(['total_sessions' => 1])
            ->assertJsonFragment(['duration_seconds' => 300]);
    }

    public function test_history_requires_auth(): void
    {
        $this
            ->getJson(route('reading.history'))
            ->assertStatus(401);
    }

    public function test_history_isolates_between_users(): void
    {
        /** @var UserModel $otherUser */
        $otherUser = UserModel::factory()->create([
            'role' => RoleEnum::READER,
        ]);
        $otherUserToken = $otherUser->createToken('reader-token')->plainTextToken;
        /** @var BookModel $book */
        $book = BookModel::factory()->create([
            'access_type' => AccessTypeEnum::FREE,
        ]);
        /** @var BookChapterModel $chapter */
        $chapter = BookChapterModel::factory()
            ->for($book, 'book')
            ->create();
        /** @var BookPageModel $page */
        $page = BookPageModel::factory()
            ->for($chapter, 'chapter')
            ->create();

        $started = $this->withToken($otherUserToken)
            ->postJson(route('reading.session.start', ['bookId' => $book->id]), []);

        $this->withToken($otherUserToken)
            ->patchJson(route('reading.session.end', [
                'bookId'    => $book->id,
                'sessionId' => $started->json('session_id'),
            ]), ['end_page_id' => $page->id, 'duration_seconds' => 60]);

        $this->resetAuthState();

        $this->withToken($this->token)
            ->getJson(route('reading.history'))
            ->assertJsonFragment(['total_sessions' => 0]);
    }

    private function resetAuthState(): void
    {
        $this->app['auth']->forgetGuards();
    }
}
