<?php

namespace App\Infrastructure\Search;

use App\Application\Catalog\DTOs\BookSearchHit;
use App\Application\Catalog\DTOs\BookSearchQuery;
use App\Application\Catalog\DTOs\BookSearchResult;
use App\Application\Catalog\Interfaces\BookSearchIndexInterface;
use App\Domain\Catalog\Entities\Book;
use Meilisearch\Client;
use Meilisearch\Exceptions\ApiException;
use RuntimeException;

final class MeilisearchBookIndex implements BookSearchIndexInterface
{
    private const string INDEX_NAME = 'books';

    public function __construct(
        private readonly Client $client,
        private readonly MeilisearchTaskAwaiter $awaiter,
    ) {}

    public function index(Book $book): void
    {
        $this->client
            ->index(self::INDEX_NAME)
            ->addDocuments([$this->toDocument($book)]);
    }

    public function delete(int $bookId): void
    {
        try {
            $this->client
                ->index(self::INDEX_NAME)
                ->deleteDocument($bookId);
        } catch (ApiException $e) {
            if ('document_not_found' !== $e->errorCode) {
                throw new RuntimeException(
                    "Failed to delete book #{$bookId} from search index: {$e->getMessage()}",
                    previous: $e,
                );
            }
        }
    }

    public function search(BookSearchQuery $query): BookSearchResult
    {
        $result = $this->client
            ->index(self::INDEX_NAME)
            ->search($query->query, $this->buildSearchParams($query));

        return new BookSearchResult(
            hits: array_map(
                fn(array $hit) => $this->toHit($hit),
                $result->getHits(),
            ),
            total: $result->getEstimatedTotalHits() ?? 0,
            limit: $result->getLimit()             ?? $query->limit,
            offset: $result->getOffset()            ?? $query->offset,
            processingTimeMs: $result->getProcessingTimeMs()  ?? 0,
        );
    }

    public function bulkIndex(array $books): void
    {
        if (empty($books)) {
            return;
        }

        $taskUids = [];

        foreach (array_chunk($books, 1000) as $batch) {
            $task = $this->client
                ->index(self::INDEX_NAME)
                ->addDocuments(
                    array_map(fn(Book $b) => $this->toDocument($b), $batch),
                );

            $taskUids[] = $task['taskUid'];
        }

        $this->awaiter->waitAll($taskUids);
    }

    public function reindex(array $books): void
    {
        $task = $this->client
            ->index(self::INDEX_NAME)
            ->deleteAllDocuments();

        $this->awaiter->wait($task['taskUid']);

        if ( ! empty($books)) {
            $this->bulkIndex($books);
        }
    }

    private function buildSearchParams(BookSearchQuery $query): array
    {
        $params = [
            'limit'                 => $query->limit,
            'offset'                => $query->offset,
            'attributesToHighlight' => ['title', 'description'],
            'highlightPreTag'       => '<mark>',
            'highlightPostTag'      => '</mark>',
            'showRankingScore'      => true,
        ];

        $filters = $this->buildFilters($query);
        if ( ! empty($filters)) {
            $params['filter'] = $filters;
        }

        return $params;
    }

    private function buildFilters(BookSearchQuery $query): array
    {
        $filters = [];

        if (null !== $query->status) {
            $filters[] = "status = '{$query->status->value}'";
        }

        if (null !== $query->accessType) {
            $filters[] = "access_type = '{$query->accessType->value}'";
        }

        if (null !== $query->language) {
            // Значения с пробелами/спецсимволами берём в кавычки
            $filters[] = "language = '{$query->language}'";
        }

        return $filters;
    }

    private function toDocument(Book $book): array
    {
        return [
            'id'          => $book->id,
            'title'       => $book->title,
            'slug'        => $book->slug,
            'description' => $book->description ?? '',
            'isbn'        => $book->isbn ?? '',
            'language'    => $book->language,
            'access_type' => $book->accessType->value,
            'status'      => $book->status->value,
        ];
    }

    private function toHit(array $hit): BookSearchHit
    {
        return new BookSearchHit(
            bookId: $hit['id'],
            title: $hit['_formatted']['title']       ?? $hit['title'],
            slug: $hit['slug'],
            description: $hit['_formatted']['description'] ?? $hit['description'] ?? null,
            accessType: $hit['access_type'],
            status: $hit['status'],
            rankingScore: $hit['_rankingScore'] ?? 0.0,
        );
    }
}
