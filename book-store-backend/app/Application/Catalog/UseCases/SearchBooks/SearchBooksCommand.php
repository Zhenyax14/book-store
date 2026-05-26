<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\SearchBooks;

use App\Application\Catalog\DTOs\BookSearchQuery;
use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;

final readonly class SearchBooksCommand
{
    private const int DEFAULT_LIMIT    = 20;

    private const int DEFAULT_OFFSET   = 0;

    public function __construct(
        public string          $query,
        public ?BookStatusEnum $status     = null,
        public ?AccessTypeEnum $accessType = null,
        public ?string         $language   = null,
        public int             $limit      = self::DEFAULT_LIMIT,
        public int             $offset     = self::DEFAULT_OFFSET,
    ) {}

    public static function fromArray(array $validated): self
    {
        return new self(
            query: $validated['q'] ?? '',
            status: isset($validated['status'])
                ? BookStatusEnum::from($validated['status'])
                : null,
            accessType: isset($validated['access_type'])
                ? AccessTypeEnum::from($validated['access_type'])
                : null,
            language: $validated['language'] ?? null,
            limit: isset($validated['limit']) ? (int) $validated['limit'] : self::DEFAULT_LIMIT,
            offset: isset($validated['offset']) ? (int) $validated['offset'] : self::DEFAULT_OFFSET,
        );
    }

    public function toQuery(): BookSearchQuery
    {
        return new BookSearchQuery(
            query: $this->query,
            status: $this->status,
            accessType: $this->accessType,
            language: $this->language,
            limit: $this->limit,
            offset: $this->offset,
        );
    }
}
