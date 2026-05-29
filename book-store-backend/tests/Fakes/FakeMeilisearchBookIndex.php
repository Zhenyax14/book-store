<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Application\Catalog\DTOs\BookSearchQuery;
use App\Application\Catalog\DTOs\BookSearchResult;
use App\Application\Catalog\Interfaces\BookSearchIndexInterface;
use App\Domain\Catalog\Entities\Book;
use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use PHPUnit\Framework\Assert;

final class FakeMeilisearchBookIndex implements BookSearchIndexInterface
{
    /** @var array<int, array> */
    private array $indexed = [];

    private ?BookSearchQuery $lastQuery = null;

    // Очередь заготовленных результатов — pop при каждом search()
    /** @var BookSearchResult[] */
    private array $results = [];

    // ──────────────────────────────────────────────────────────────
    // Interface implementation
    // ──────────────────────────────────────────────────────────────

    public function index(Book $book): void
    {
        $this->indexed[$book->id] = ['id' => $book->id, 'title' => $book->title];
    }

    public function delete(int $bookId): void
    {
        unset($this->indexed[$bookId]);
    }

    public function search(BookSearchQuery $query): BookSearchResult
    {
        $this->lastQuery = $query;

        // Если подготовлен результат — возвращаем его
        if ( ! empty($this->results)) {
            return array_shift($this->results);
        }

        // Иначе — простая in-memory фильтрация по title
        $hits = array_filter(
            $this->indexed,
            static fn($doc) => '' === $query->query
                || str_contains(mb_strtolower($doc['title']), mb_strtolower($query->query)),
        );

        return new BookSearchResult(
            hits: [],
            total: count($hits),
            limit: $query->limit,
            offset: $query->offset,
            processingTimeMs: 0,
        );
    }

    public function bulkIndex(array $books): void
    {
        foreach ($books as $book) {
            $this->index($book);
        }
    }

    public function reindex(array $books): void
    {
        $this->indexed = [];
        $this->bulkIndex($books);
    }

    // ──────────────────────────────────────────────────────────────
    // Test setup
    // ──────────────────────────────────────────────────────────────

    public function pushResult(BookSearchResult $result): void
    {
        $this->results[] = $result;
    }

    // ──────────────────────────────────────────────────────────────
    // Assertions: indexed documents
    // ──────────────────────────────────────────────────────────────

    public function assertIndexed(int $bookId): void
    {
        Assert::assertArrayHasKey($bookId, $this->indexed, "Book #{$bookId} was not indexed.");
    }

    public function assertNotIndexed(int $bookId): void
    {
        Assert::assertArrayNotHasKey($bookId, $this->indexed, "Book #{$bookId} was unexpectedly indexed.");
    }

    // ──────────────────────────────────────────────────────────────
    // Assertions: last search query
    // ──────────────────────────────────────────────────────────────

    public function assertLastQueryEquals(string $expected): void
    {
        Assert::assertNotNull($this->lastQuery, 'No search was performed.');
        Assert::assertSame($expected, $this->lastQuery->query);
    }

    public function assertLastStatusEquals(?BookStatusEnum $expected): void
    {
        Assert::assertNotNull($this->lastQuery, 'No search was performed.');
        Assert::assertEquals($expected, $this->lastQuery->status);
    }

    public function assertLastAccessTypeEquals(?AccessTypeEnum $expected): void
    {
        Assert::assertNotNull($this->lastQuery, 'No search was performed.');
        Assert::assertEquals($expected, $this->lastQuery->accessType);
    }

    public function assertLastLanguageEquals(?string $expected): void
    {
        Assert::assertNotNull($this->lastQuery, 'No search was performed.');
        Assert::assertSame($expected, $this->lastQuery->language);
    }

    public function assertLastLimitEquals(int $expected): void
    {
        Assert::assertNotNull($this->lastQuery, 'No search was performed.');
        Assert::assertSame($expected, $this->lastQuery->limit);
    }

    public function assertLastOffsetEquals(int $expected): void
    {
        Assert::assertNotNull($this->lastQuery, 'No search was performed.');
        Assert::assertSame($expected, $this->lastQuery->offset);
    }
}
