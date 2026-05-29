<?php

namespace Tests\Feature\Catalog;

use App\Application\Catalog\DTOs\BookSearchHit;
use App\Application\Catalog\DTOs\BookSearchResult;
use App\Application\Catalog\Interfaces\BookSearchIndexInterface;
use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fakes\FakeMeilisearchBookIndex;
use Tests\TestCase;

final class BookSearchTest extends TestCase
{
    use DatabaseTransactions;

    private FakeMeilisearchBookIndex $searchIndex;

    protected function setUp(): void
    {
        parent::setUp();

        $this->searchIndex = new FakeMeilisearchBookIndex();
        $this->instance(BookSearchIndexInterface::class, $this->searchIndex);
    }

    // ──────────────────────────────────────────────────────────────
    // Response structure
    // ──────────────────────────────────────────────────────────────

    public function test_search_returns_200(): void
    {
        $this->getJson(route('books.search', ['q' => 'php']))
            ->assertStatus(200);
    }

    public function test_search_returns_expected_structure(): void
    {
        $this->getJson(route('books.search', ['q' => 'php']))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'total',
                    'limit',
                    'offset',
                    'processing_time_ms',
                ],
            ]);
    }

    public function test_search_returns_hits_with_expected_fields(): void
    {
        $this->searchIndex->pushResult(
            $this->makeSearchResult([$this->makeHit(bookId: 1, title: 'PHP 8 in Depth')]),
        );

        $response = $this->getJson(route('books.search', ['q' => 'php']));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'slug', 'description', 'access_type', 'status', 'ranking_score'],
                ],
            ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Search query passthrough
    // ──────────────────────────────────────────────────────────────

    public function test_search_passes_query_to_index(): void
    {
        $this->getJson(route('books.search', ['q' => 'clean architecture']));

        $this->searchIndex->assertLastQueryEquals('clean architecture');
    }

    public function test_search_without_q_uses_empty_string(): void
    {
        $this->getJson(route('books.search'));

        $this->searchIndex->assertLastQueryEquals('');
    }

    // ──────────────────────────────────────────────────────────────
    // Filters passthrough
    // ──────────────────────────────────────────────────────────────

    public function test_search_passes_status_filter(): void
    {
        $this->getJson(route('books.search', [
            'q'      => 'php',
            'status' => BookStatusEnum::PUBLISHED->value,
        ]));

        $this->searchIndex->assertLastStatusEquals(BookStatusEnum::PUBLISHED);
    }

    public function test_search_passes_access_type_filter(): void
    {
        $this->getJson(route('books.search', [
            'q'           => 'php',
            'access_type' => AccessTypeEnum::FREE->value,
        ]));

        $this->searchIndex->assertLastAccessTypeEquals(AccessTypeEnum::FREE);
    }

    public function test_search_passes_language_filter(): void
    {
        $this->getJson(route('books.search', [
            'q'        => 'php',
            'language' => 'en',
        ]));

        $this->searchIndex->assertLastLanguageEquals('en');
    }

    public function test_search_passes_limit_and_offset(): void
    {
        $this->getJson(route('books.search', [
            'q'      => 'php',
            'limit'  => 10,
            'offset' => 40,
        ]));

        $this->searchIndex->assertLastLimitEquals(10);
        $this->searchIndex->assertLastOffsetEquals(40);
    }

    // ──────────────────────────────────────────────────────────────
    // Meta passthrough
    // ──────────────────────────────────────────────────────────────

    public function test_search_meta_reflects_result_from_index(): void
    {
        $this->searchIndex->pushResult(
            new BookSearchResult(
                hits: [],
                total: 42,
                limit: 10,
                offset: 20,
                processingTimeMs: 5,
            ),
        );

        $response = $this->getJson(route('books.search', ['q' => 'php']));

        $response->assertJsonFragment([
            'total'              => 42,
            'limit'              => 10,
            'offset'             => 20,
            'processing_time_ms' => 5,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Validation
    // ──────────────────────────────────────────────────────────────

    public function test_search_rejects_q_exceeding_max_length(): void
    {
        $this->getJson(route('books.search', ['q' => str_repeat('a', 101)]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['q']);
    }

    public function test_search_rejects_invalid_status(): void
    {
        $this->getJson(route('books.search', ['status' => 'invalid_status']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_search_rejects_invalid_access_type(): void
    {
        $this->getJson(route('books.search', ['access_type' => 'invalid']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['access_type']);
    }

    public function test_search_rejects_language_longer_than_two_chars(): void
    {
        $this->getJson(route('books.search', ['language' => 'eng']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['language']);
    }

    public function test_search_rejects_uppercase_language(): void
    {
        $this->getJson(route('books.search', ['language' => 'EN']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['language']);
    }

    public function test_search_rejects_limit_above_max(): void
    {
        $this->getJson(route('books.search', ['limit' => 101]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    public function test_search_rejects_negative_offset(): void
    {
        $this->getJson(route('books.search', ['offset' => -1]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['offset']);
    }

    public function test_search_accepts_all_valid_filters_together(): void
    {
        $this->getJson(route('books.search', [
            'q'           => 'php',
            'status'      => BookStatusEnum::PUBLISHED->value,
            'access_type' => AccessTypeEnum::FREE->value,
            'language'    => 'en',
            'limit'       => 10,
            'offset'      => 0,
        ]))->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────

    private function makeSearchResult(array $hits = []): BookSearchResult
    {
        return new BookSearchResult(
            hits: $hits,
            total: count($hits),
            limit: 20,
            offset: 0,
            processingTimeMs: 1,
        );
    }

    private function makeHit(int $bookId = 1, string $title = 'Test Book'): BookSearchHit
    {
        return new BookSearchHit(
            bookId: $bookId,
            title: $title,
            slug: 'test-book',
            description: null,
            accessType: AccessTypeEnum::FREE->value,
            status: BookStatusEnum::PUBLISHED->value,
            rankingScore: 1.0,
        );
    }
}
