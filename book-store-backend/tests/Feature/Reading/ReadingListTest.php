<?php

declare(strict_types=1);

namespace Tests\Feature\Reading;

use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class ReadingListTest extends TestCase
{
    use DatabaseTransactions;

    private UserModel $user;

    private string    $token;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserModel $user */
        $this->user  = UserModel::factory()->create();
        $this->token = $this->user->createToken('reader-token')->plainTextToken;
    }

    public function test_index_returns_200_with_pagination_structure(): void
    {
        $this->withToken($this->token)
            ->getJson(route('reading-list.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['total', 'per_page', 'current_page', 'total_pages'],
            ]);
    }

    public function test_index_returns_only_current_user_entries(): void
    {
        /** @var UserModel $other */
        $other     = UserModel::factory()->create();
        /** @var BookModel $otherBook */
        $otherBook = BookModel::factory()->create();
        /** @var BookModel $ownBook */
        $ownBook   = BookModel::factory()->create();

        $this->withToken($other->createToken('reader-token')->plainTextToken)
            ->postJson(route('reading-list.store'), ['book_id' => $otherBook->id]);

        $this->resetAuthState();

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $ownBook->id]);

        $this->withToken($this->token)
            ->getJson(route('reading-list.index'))
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.book_id', $ownBook->id);
    }

    public function test_index_can_be_filtered_by_status(): void
    {
        /** @var BookModel $bookA */
        $bookA = BookModel::factory()->create();
        /** @var BookModel $bookB */
        $bookB = BookModel::factory()->create();

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $bookA->id]);

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $bookB->id]);

        $this->withToken($this->token)
            ->patchJson(route('reading-list.start', ['bookId' => $bookA->id]), ['total_pages' => 100]);

        $this->withToken($this->token)
            ->getJson(route('reading-list.index', ['status' => 'reading']))
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.book_id', $bookA->id);
    }

    public function test_index_requires_auth(): void
    {
        $this->getJson(route('reading-list.index'))->assertUnauthorized();
    }

    public function test_store_adds_book_with_want_to_read_status(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $book->id])
            ->assertCreated()
            ->assertJsonPath('status', ReadingStatusEnum::WANT_TO_READ->value)
            ->assertJsonPath('book_id', $book->id);

        $this->assertDatabaseHas('user_reading_list', [
            'user_id' => $this->user->id,
            'book_id' => $book->id,
            'status'  => ReadingStatusEnum::WANT_TO_READ->value,
        ]);
    }

    public function test_store_returns_409_when_book_already_in_list(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $book->id]);

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $book->id])
            ->assertConflict();
    }

    public function test_store_returns_422_when_book_not_found(): void
    {
        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => 99999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['book_id']);
    }

    public function test_store_returns_422_when_book_id_missing(): void
    {
        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['book_id']);
    }

    public function test_start_transitions_to_reading_status(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $book->id]);

        $this->withToken($this->token)
            ->patchJson(route('reading-list.start', ['bookId' => $book->id]), ['total_pages' => 300])
            ->assertOk()
            ->assertJsonPath('status', ReadingStatusEnum::READING->value)
            ->assertJsonPath('total_pages', 300)
            ->assertJsonPath('current_page', 0);
    }

    public function test_start_returns_404_when_entry_not_found(): void
    {
        $this->withToken($this->token)
            ->patchJson(route('reading-list.start', ['bookId' => 99999]), ['total_pages' => 100])
            ->assertNotFound();
    }

    public function test_start_returns_422_on_invalid_status_transition(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $book->id]);

        $this->withToken($this->token)
            ->patchJson(route('reading-list.start', ['bookId' => $book->id]), ['total_pages' => 100]);

        $this->withToken($this->token)
            ->patchJson(route('reading-list.start', ['bookId' => $book->id]), ['total_pages' => 100])
            ->assertUnprocessable();
    }

    public function test_start_returns_422_when_total_pages_missing(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this->withToken($this->token)
            ->patchJson(route('reading-list.start', ['bookId' => $book->id]), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['total_pages']);
    }

    public function test_progress_updates_current_page(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $book->id]);

        $this->withToken($this->token)
            ->patchJson(route('reading-list.start', ['bookId' => $book->id]), ['total_pages' => 300]);

        $this->withToken($this->token)
            ->patchJson(route('reading-list.progress', ['bookId' => $book->id]), ['current_page' => 150])
            ->assertOk()
            ->assertJsonPath('current_page', 150)
            ->assertJsonPath('status', ReadingStatusEnum::READING->value);
    }

    public function test_progress_auto_finishes_on_last_page(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $book->id]);

        $this->withToken($this->token)
            ->patchJson(route('reading-list.start', ['bookId' => $book->id]), ['total_pages' => 200]);

        $this->withToken($this->token)
            ->patchJson(route('reading-list.progress', ['bookId' => $book->id]), ['current_page' => 200])
            ->assertOk()
            ->assertJsonPath('status', ReadingStatusEnum::FINISHED->value);

        $this->assertDatabaseHas('user_reading_list', [
            'user_id' => $this->user->id,
            'book_id' => $book->id,
            'status'  => ReadingStatusEnum::FINISHED->value,
        ]);
    }

    public function test_progress_returns_404_when_entry_not_found(): void
    {
        $this->withToken($this->token)
            ->patchJson(route('reading-list.progress', ['bookId' => 99999]), ['current_page' => 10])
            ->assertNotFound();
    }

    public function test_destroy_removes_entry_and_returns_204(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this->withToken($this->token)
            ->postJson(route('reading-list.store'), ['book_id' => $book->id]);

        $this->withToken($this->token)
            ->deleteJson(route('reading-list.destroy', ['bookId' => $book->id]))
            ->assertNoContent();

        $this->assertDatabaseMissing('user_reading_list', [
            'user_id' => $this->user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_destroy_returns_404_when_entry_not_found(): void
    {
        $this->withToken($this->token)
            ->deleteJson(route('reading-list.destroy', ['bookId' => 99999]))
            ->assertNotFound();
    }

    public function test_destroy_does_not_affect_other_users_entries(): void
    {
        /** @var UserModel $other */
        $other     = UserModel::factory()->create();
        $otherToken = $other->createToken('reader-token')->plainTextToken;
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this->withToken($otherToken)
            ->postJson(route('reading-list.store'), ['book_id' => $book->id]);

        $this->resetAuthState();

        $this->withToken($this->token)
            ->deleteJson(route('reading-list.destroy', ['bookId' => $book->id]))
            ->assertNotFound();

        $this->assertDatabaseHas('user_reading_list', [
            'user_id' => $other->id,
            'book_id' => $book->id,
        ]);
    }

    private function resetAuthState(): void
    {
        $this->app['auth']->forgetGuards();
    }
}
