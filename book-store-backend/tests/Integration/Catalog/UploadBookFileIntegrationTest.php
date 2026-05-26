<?php

declare(strict_types=1);

namespace Tests\Integration\Catalog;

use App\Application\Catalog\Jobs\ParseBookFileJob;
use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\RequiresMinIO;

final class UploadBookFileIntegrationTest extends TestCase
{
    use RefreshDatabase;
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

        Queue::fake();
    }

    protected function tearDown(): void
    {
        $this->cleanupBucket();
        parent::tearDown();
    }

    public function test_pdf_is_physically_stored_in_minio(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                ['book_file' => $file],
            );

        $filePath = $response->json('file_path');

        $this->assertTrue(
            Storage::disk('s3')->exists($filePath),
            "File {$filePath} not found in MinIO",
        );
    }

    public function test_epub_is_physically_stored_in_minio(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->create('book.epub', 100, 'application/epub+zip');

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                ['book_file' => $file],
            );

        $filePath = $response->json('file_path');

        $this->assertTrue(
            Storage::disk('s3')->exists($filePath),
            "File {$filePath} not found in MinIO",
        );
    }

    public function test_file_content_matches_original(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');

        $originalContent = file_get_contents($file->getRealPath());

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                ['book_file' => $file],
            );

        $filePath = $response->json('file_path');
        $storedContent = Storage::disk('s3')->get($filePath);

        $this->assertEquals(
            md5($originalContent),
            md5($storedContent),
            'The content of the file in MinIO does not match the original',
        );
    }

    public function test_parse_job_receives_correct_file_path(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                ['book_file' => $file],
            );

        $filePath = $response->json('file_path');

        Queue::assertPushed(
            ParseBookFileJob::class,
            static fn($job) => $job->filePath === $filePath,
        );
    }

    public function test_file_path_follows_expected_structure(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->create('my-book.pdf', 100, 'application/pdf');

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                ['book_file' => $file],
            );

        $filePath = $response->json('file_path');

        $this->assertMatchesRegularExpression(
            "/^books\/{$book->id}\/.+\.pdf$/",
            $filePath,
        );
    }

    private function cleanupBucket(): void
    {
        $files = Storage::disk('s3')->allFiles('books');

        foreach ($files as $file) {
            Storage::disk('s3')->delete($file);
        }
    }
}
