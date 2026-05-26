<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Application\Catalog\Interfaces\BookFileStorageInterface;
use App\Application\Catalog\Jobs\ParseBookFileJob;
use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\Fakes\FakeBookFileStorage;
use Tests\TestCase;

final class UploadBookFileTest extends TestCase
{
    use DatabaseTransactions;

    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->instance(BookFileStorageInterface::class, new FakeBookFileStorage());

        /** @var UserModel $admin */
        $admin = UserModel::factory()->create(['role' => RoleEnum::ADMIN]);
        $this->adminToken = $admin->createToken('admin-token')->plainTextToken;
    }

    public function test_upload_pdf_returns_202(): void
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

        $response->assertStatus(202);
    }

    public function test_upload_epub_returns_202(): void
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

        $response->assertStatus(202);
    }

    public function test_response_contains_expected_structure(): void
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

        $response->assertStatus(202)
            ->assertJsonStructure(['book_id', 'file_path', 'status'])
            ->assertJsonFragment([
                'book_id' => $book->id,
                'status' => 'processing',
            ]);
    }

    public function test_parse_job_is_dispatched(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                ['book_file' => $file],
            );

        Queue::assertPushed(
            ParseBookFileJob::class,
            static fn($job) => $job->bookId === $book->id
                && 'application/pdf' === $job->mimeType,
        );
    }

    public function test_parse_job_is_dispatched_only_once(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                ['book_file' => $file],
            );

        Queue::assertPushedTimes(ParseBookFileJob::class, 1);
    }

    public function test_requires_book_file(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                [],
            );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_file']);
    }

    public function test_rejects_image_file(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg');

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                ['book_file' => $file],
            );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_file']);
    }

    public function test_rejects_file_over_size_limit(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();
        $file = UploadedFile::fake()->create('book.pdf', 102401, 'application/pdf');

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => $book->id]),
                ['book_file' => $file],
            );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_file']);
    }

    public function test_accepts_pdf_and_epub_only(): void
    {
        /** @var BookModel $book */
        $book = BookModel::factory()->create();

        foreach (['application/pdf' => 'book.pdf', 'application/epub+zip' => 'book.epub'] as $mime => $filename) {
            $file = UploadedFile::fake()->create($filename, 100, $mime);

            $response = $this
                ->withToken($this->adminToken)
                ->postJson(
                    route('admin.books.file', ['id' => $book->id]),
                    ['book_file' => $file],
                );

            $response->assertStatus(202, "Format {$mime} must be allowed");
        }
    }

    public function test_returns_404_for_nonexistent_book(): void
    {
        $file = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');

        $response = $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => 99999]),
                ['book_file' => $file],
            );

        $response->assertStatus(404);
    }

    public function test_job_is_not_dispatched_for_nonexistent_book(): void
    {
        $file = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');

        $this
            ->withToken($this->adminToken)
            ->postJson(
                route('admin.books.file', ['id' => 99999]),
                ['book_file' => $file],
            );

        Queue::assertNothingPushed();
    }
}
