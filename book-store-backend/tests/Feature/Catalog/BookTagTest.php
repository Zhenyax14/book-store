<?php

namespace Tests\Feature\Catalog;

use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\TagModel;
use App\Infrastructure\Persistence\Models\UserModel;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class BookTagTest extends TestCase
{
    use DatabaseTransactions;

    private string $adminToken;

    private string $readerToken;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var UserModel $admin */
        $admin  = UserModel::factory()->create(['role' => RoleEnum::ADMIN]);
        $this->adminToken = $admin->createToken('admin-token')->plainTextToken;

        /** @var UserModel $reader */
        $reader  = UserModel::factory()->create(['role' => RoleEnum::READER]);
        $this->readerToken = $reader->createToken('reader-token')->plainTextToken;
    }

    public function test_sync_replaces_all_tags_and_returns_204(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        /** @var Collection<TagModel> $tags */
        $tags = TagModel::factory()->count(3)->create();

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.sync', ['id' => $book->id]),
                ['tag_ids' => $tags->pluck('id')->toArray()],
            )->assertStatus(204);
    }

    public function test_sync_persists_tags_to_database(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        /** @var Collection<TagModel> $tags */
        $tags = TagModel::factory()->count(2)->create();

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.sync', ['id' => $book->id]),
                ['tag_ids' => $tags->pluck('id')->toArray()],
            );

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('book_tag', [
                'book_id' => $book->id,
                'tag_id'  => $tag->id,
            ]);
        }
    }

    public function test_sync_removes_old_tags_not_in_new_list(): void
    {
        /** @var BookModel $book */
        $book    = BookModel::factory()->create();
        /** @var Collection<TagModel> $oldTags */
        $oldTags = TagModel::factory()->count(2)->create();
        /** @var TagModel $newTag */
        $newTag  = TagModel::factory()->create();

        $book->tags()->sync($oldTags->pluck('id')->toArray());

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.sync', ['id' => $book->id]),
                ['tag_ids' => [$newTag->id]],
            );

        foreach ($oldTags as $tag) {
            $this->assertDatabaseMissing('book_tag', [
                'book_id' => $book->id,
                'tag_id'  => $tag->id,
            ]);
        }

        $this->assertDatabaseHas('book_tag', [
            'book_id' => $book->id,
            'tag_id'  => $newTag->id,
        ]);
    }

    public function test_sync_with_empty_array_detaches_all_tags(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        /** @var Collection<TagModel> $tags */
        $tags = TagModel::factory()->count(2)->create();
        $book->tags()->sync($tags->pluck('id')->toArray());

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.sync', ['id' => $book->id]),
                ['tag_ids' => []],
            )->assertStatus(204);

        foreach ($tags as $tag) {
            $this->assertDatabaseMissing('book_tag', [
                'book_id' => $book->id,
                'tag_id'  => $tag->id,
            ]);
        }
    }

    public function test_sync_returns_422_when_tag_ids_missing(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.sync', ['id' => $book->id]),
                [],
            )->assertStatus(422)
            ->assertJsonValidationErrors(['tag_ids']);
    }

    public function test_sync_returns_422_when_tag_not_found(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.sync', ['id' => $book->id]),
                ['tag_ids' => [99999]],
            )->assertStatus(422);
    }

    public function test_sync_returns_404_when_book_not_found(): void
    {
        /** @var TagModel $tag */
        $tag = TagModel::factory()->create();

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.sync', ['id' => 99999]),
                ['tag_ids' => [$tag->id]],
            )->assertStatus(404);
    }

    public function test_attach_adds_tag_to_book_and_returns_204(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        /** @var TagModel $tag */
        $tag  = TagModel::factory()->create();

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.attach', ['id' => $book->id, 'tagId' => $tag->id]),
            )->assertStatus(204);

        $this->assertDatabaseHas('book_tag', [
            'book_id' => $book->id,
            'tag_id'  => $tag->id,
        ]);
    }

    public function test_attach_is_idempotent(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        /** @var TagModel $tag */
        $tag  = TagModel::factory()->create();

        $this
            ->withToken($this->adminToken)
            ->postJson(route('admin.books.tags.attach', ['id' => $book->id, 'tagId' => $tag->id]));
        $this
            ->withToken($this->adminToken)
            ->postJson(route('admin.books.tags.attach', ['id' => $book->id, 'tagId' => $tag->id]))
            ->assertStatus(204);

        $this->assertSame(
            1,
            DB::table('book_tag')
                ->where('book_id', $book->id)
                ->where('tag_id', $tag->id)
                ->count(),
        );
    }

    public function test_attach_returns_404_when_book_not_found(): void
    {
        $tag = TagModel::factory()->create();

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.attach', ['id' => 99999, 'tagId' => $tag->id]),
            )->assertStatus(404);
    }

    public function test_attach_returns_404_when_tag_not_found(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.tags.attach', ['id' => $book->id, 'tagId' => 99999]),
            )->assertStatus(404);
    }

    public function test_detach_removes_tag_from_book_and_returns_204(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        /** @var TagModel $tag */
        $tag  = TagModel::factory()->create();
        $book->tags()->attach($tag->id);

        $this
            ->withToken($this->adminToken)
            ->deleteJson(
                route('admin.books.tags.detach', ['id' => $book->id, 'tagId' => $tag->id]),
            )->assertStatus(204);

        $this->assertDatabaseMissing('book_tag', [
            'book_id' => $book->id,
            'tag_id'  => $tag->id,
        ]);
    }

    public function test_detach_is_idempotent(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        /** @var TagModel $tag */
        $tag  = TagModel::factory()->create();

        $this
            ->withToken($this->adminToken)
            ->deleteJson(
                route('admin.books.tags.detach', ['id' => $book->id, 'tagId' => $tag->id]),
            )->assertStatus(204);
    }

    public function test_detach_does_not_affect_other_tags(): void
    {
        /** @var BookModel $book */
        $book      = BookModel::factory()->create();
        /** @var TagModel $keepTag */
        $keepTag   = TagModel::factory()->create();
        /** @var TagModel $removeTag */
        $removeTag = TagModel::factory()->create();

        $book->tags()->sync([$keepTag->id, $removeTag->id]);

        $this
            ->withToken($this->adminToken)
            ->deleteJson(
                route('admin.books.tags.detach', ['id' => $book->id, 'tagId' => $removeTag->id]),
            );

        $this->assertDatabaseHas('book_tag', [
            'book_id' => $book->id,
            'tag_id'  => $keepTag->id,
        ]);

        $this->assertDatabaseMissing('book_tag', [
            'book_id' => $book->id,
            'tag_id'  => $removeTag->id,
        ]);
    }

    public function test_detach_returns_404_when_book_not_found(): void
    {
        /** @var TagModel $tag */
        $tag = TagModel::factory()->create();

        $this
            ->withToken($this->adminToken)
            ->deleteJson(
                route('admin.books.tags.detach', ['id' => 99999, 'tagId' => $tag->id]),
            )->assertStatus(404);
    }
}
