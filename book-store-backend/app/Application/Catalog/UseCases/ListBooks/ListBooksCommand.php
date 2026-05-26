<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\ListBooks;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;

final readonly class ListBooksCommand
{
    private const int DEFAULT_PER_PAGE = 20;

    private const int DEFAULT_PAGE     = 1;

    public function __construct(
        public ?BookStatusEnum $status = null,
        public ?AccessTypeEnum $accessType = null,
        public ?string         $language = null,
        public ?string         $search = null,
        public ?int            $perPage = null,
        public ?int            $page = null,
        public ?string $tag = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: ! empty($data['status']) ? BookStatusEnum::from($data['status']) : null,
            accessType: ! empty($data['access_type']) ? AccessTypeEnum::from($data['access_type']) : null,
            language: $data['language'] ?? null,
            search: $data['search'] ?? null,
            perPage: ! empty($data['per_page']) ? (int) $data['per_page'] : self::DEFAULT_PER_PAGE,
            page: ! empty($data['page']) ? (int) $data['page'] : self::DEFAULT_PAGE,
            tag: $data['tag'] ?? null,
        );
    }
}
