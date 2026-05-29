<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Fakes\FakeBookCoverStorage;
use Tests\TestCase;

final class UploadBookCoverTest extends TestCase
{
    use DatabaseTransactions;

    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('s3');

        $this->instance(BookCoverStorageInterface::class, new FakeBookCoverStorage());
        /** @var UserModel $admin */
        $admin  = UserModel::factory()->create(['role' => RoleEnum::ADMIN]);
        $this->adminToken = $admin->createToken('admin-token')->plainTextToken;
    }

    public function test_upload_cover_returns_200(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()
            ->image('cover.jpg', 800, 600);

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.cover', ['id' => $book->id,]),
                ['cover' => $file],
            );

        $response->assertStatus(200);
    }

    public function test_upload_cover_updates_cover_path_in_database(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create(['cover_path' => null]);
        $file = UploadedFile::fake()->image('cover.jpg', 800, 600);

        $this
            ->withToken($this->adminToken)->postJson(
                route('admin.books.cover', $book->id),
                ['cover' => $file],
            );

        $this->assertDatabaseMissing('books', [
            'id'         => $book->id,
            'cover_path' => null,
        ]);
    }

    public function test_upload_cover_response_contains_cover_url(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg');

        $response = $this
            ->withToken($this->adminToken)->postJson(
                route('admin.books.cover', $book->id),
                ['cover' => $file],
            );

        $response->assertStatus(200)
            ->assertJsonStructure(['cover_url']);
    }

    public function test_upload_cover_replaces_old_cover(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $book->update(['cover_path' => 'covers/' . $book->id . '/old-cover.jpg']);

        $file = UploadedFile::fake()->image('new-cover.jpg');

        $this
            ->withToken($this->adminToken)->postJson(
                route('admin.books.cover', $book->id),
                ['cover' => $file],
            );

        $this->assertDatabaseMissing('books', [
            'id'         => $book->id,
            'cover_path' => 'covers/' . $book->id . '/old-cover.jpg',
        ]);
    }

    public function test_upload_cover_requires_file(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(route('admin.books.cover', $book->id), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cover']);
    }

    public function test_upload_cover_rejects_non_image(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.cover', $book->id),
                ['cover' => $file],
            );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cover']);
    }

    public function test_upload_cover_rejects_file_over_size_limit(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $file = UploadedFile::fake()->image('cover.jpg')->size(6000);

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.cover', $book->id),
                ['cover' => $file],
            );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cover']);
    }

    public function test_upload_cover_accepts_valid_image_formats(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $format) {
            $file = UploadedFile::fake()->image("cover.{$format}");

            $response = $this
                ->withToken($this->adminToken)
                ->postJson(
                    route('admin.books.cover', $book->id),
                    ['cover' => $file],
                );

            $response->assertStatus(200, "Формат {$format} должен быть допустимым");
        }
    }

    public function test_upload_cover_returns_404_for_nonexistent_book(): void
    {
        $file = UploadedFile::fake()->image('cover.jpg');

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.cover', 99999),
                ['cover' => $file],
            );

        $response->assertStatus(404);
    }
}
