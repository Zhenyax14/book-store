<?php

declare(strict_types=1);

namespace Tests\Integration\Catalog;

use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\RequiresMinIO;

final class UploadBookCoverIntegrationTest extends TestCase
{
    use DatabaseTransactions;
    use RequiresMinIO;

    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserModel $admin */
        $admin = UserModel::factory()->create(['role' => RoleEnum::ADMIN]);
        $this->adminToken = $admin->createToken('admin-token')->plainTextToken;

        $this->ensureMinioIsAccessible();
        $this->cleanupBucket();
    }

    protected function tearDown(): void
    {
        $this->cleanupBucket();

        parent::tearDown();
    }

    public function test_cover_is_physically_uploaded_to_minio(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg', 800, 600);

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.cover', ['id' => $book->id]),
                ['cover' => $file],
            );

        $response->assertStatus(200);

        $coverPath = BookModel::find($book->id)->cover_path;

        $this->assertTrue(
            Storage::disk('s3')->exists($coverPath),
            "File {$coverPath} not found in MinIO",
        );
    }

    public function test_cover_url_is_accessible(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg', 800, 600);

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.cover', ['id' => $book->id]),
                ['cover' => $file],
            );

        $coverUrl = $response->json('cover_url');

        $this->assertNotNull($coverUrl);
        $this->assertStringContainsString('minio:9000', $coverUrl);

        $this->assertStringContainsString('/bookstore/covers/', $coverUrl);
    }

    public function test_old_cover_is_deleted_from_minio_when_new_uploaded(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $firstFile = UploadedFile::fake()->image('first-cover.jpg');

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.cover', ['id' => $book->id]),
                ['cover' => $firstFile],
            );

        $firstCoverPath = BookModel::find($book->id)->cover_path;

        $this->assertTrue(Storage::disk('s3')->exists($firstCoverPath));

        $secondFile = UploadedFile::fake()->image('second-cover.jpg');

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.cover', ['id' => $book->id]),
                ['cover' => $secondFile],
            );

        $this->assertFalse(
            Storage::disk('s3')->exists($firstCoverPath),
            "The old cover {$firstCoverPath} must be deleted from MinIO",
        );
    }

    public function test_cover_content_matches_uploaded_file(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg', 400, 600);

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.cover', ['id' => $book->id]),
                ['cover' => $file],
            );

        $coverPath = BookModel::find($book->id)->cover_path;

        $storedContent = Storage::disk('s3')->get($coverPath);
        $originalContent = file_get_contents($file->getRealPath());

        $this->assertEquals(
            md5($originalContent),
            md5($storedContent),
            'The content of the uploaded file does not match the original',
        );
    }

    private function cleanupBucket(): void
    {
        $files = Storage::disk('s3')->allFiles('covers');

        foreach ($files as $file) {
            Storage::disk('s3')->delete($file);
        }
    }
}
