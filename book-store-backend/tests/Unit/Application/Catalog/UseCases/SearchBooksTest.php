<?php

namespace Tests\Unit\Application\Catalog\UseCases;

use App\Application\Catalog\DTOs\BookSearchResult;
use App\Application\Catalog\UseCases\SearchBooks\SearchBooksHandler;
use App\Application\Catalog\UseCases\SearchBooks\SearchBooksCommand;
use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeMeilisearchBookIndex;

final class SearchBooksTest extends TestCase
{
    public function test_handler_delegates_search_to_index(): void
    {
        $index   = new FakeMeilisearchBookIndex();
        $handler = new SearchBooksHandler($index);

        $handler->handle(new SearchBooksCommand(
            query: 'php',
            limit: 20,
            offset: 0,
        ));

        $index->assertLastQueryEquals('php');
    }

    public function test_handler_returns_result_from_index(): void
    {
        $index    = new FakeMeilisearchBookIndex();
        $handler  = new SearchBooksHandler($index);
        $expected = new BookSearchResult(
            hits: [],
            total: 7,
            limit: 20,
            offset: 0,
            processingTimeMs: 3,
        );

        $index->pushResult($expected);

        $result = $handler->handle(new SearchBooksCommand(
            query: 'php',
            limit: 20,
            offset: 0,
        ));

        $this->assertSame($expected, $result);
    }

    public function test_from_array_maps_query(): void
    {
        $command = SearchBooksCommand::fromArray(['q' => 'clean code']);

        $this->assertSame('clean code', $command->query);
    }

    public function test_from_array_uses_empty_string_when_q_missing(): void
    {
        $command = SearchBooksCommand::fromArray([]);

        $this->assertSame('', $command->query);
    }

    public function test_from_array_maps_status_enum(): void
    {
        $command = SearchBooksCommand::fromArray([
            'q'      => '',
            'status' => 'published',
        ]);

        $this->assertSame(BookStatusEnum::PUBLISHED, $command->status);
    }

    public function test_from_array_maps_access_type_enum(): void
    {
        $command = SearchBooksCommand::fromArray([
            'q'           => '',
            'access_type' => 'free',
        ]);

        $this->assertSame(AccessTypeEnum::FREE, $command->accessType);
    }

    public function test_from_array_maps_language(): void
    {
        $command = SearchBooksCommand::fromArray(['q' => '', 'language' => 'ru']);

        $this->assertSame('ru', $command->language);
    }

    public function test_from_array_applies_default_limit(): void
    {
        $command = SearchBooksCommand::fromArray(['q' => '']);

        $this->assertSame(20, $command->limit);
    }

    public function test_from_array_applies_default_offset(): void
    {
        $command = SearchBooksCommand::fromArray(['q' => '']);

        $this->assertSame(0, $command->offset);
    }

    public function test_from_array_overrides_limit_and_offset(): void
    {
        $command = SearchBooksCommand::fromArray([
            'q'      => '',
            'limit'  => 5,
            'offset' => 40,
        ]);

        $this->assertSame(5, $command->limit);
        $this->assertSame(40, $command->offset);
    }

    public function test_from_array_leaves_filters_null_when_absent(): void
    {
        $command = SearchBooksCommand::fromArray(['q' => '']);

        $this->assertNull($command->status);
        $this->assertNull($command->accessType);
        $this->assertNull($command->language);
    }

    public function test_to_query_maps_all_fields(): void
    {
        $command = new SearchBooksCommand(
            query: 'ddd',
            status: BookStatusEnum::PUBLISHED,
            accessType: AccessTypeEnum::FREE,
            language: 'en',
            limit: 10,
            offset: 5,
        );

        $query = $command->toQuery();

        $this->assertSame('ddd', $query->query);
        $this->assertSame(BookStatusEnum::PUBLISHED, $query->status);
        $this->assertSame(AccessTypeEnum::FREE, $query->accessType);
        $this->assertSame('en', $query->language);
        $this->assertSame(10, $query->limit);
        $this->assertSame(5, $query->offset);
    }
}
