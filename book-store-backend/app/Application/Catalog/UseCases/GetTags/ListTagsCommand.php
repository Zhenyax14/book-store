<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\GetTags;

final readonly class ListTagsCommand
{
    private const int DEFAULT_PER_PAGE = 20;

    private const int DEFAULT_PAGE     = 1;

    public function __construct(
        public ?int $perPage = null,
        public ?int $page = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            perPage: ! empty($data['per_page']) ? (int) $data['per_page'] : self::DEFAULT_PER_PAGE,
            page: ! empty($data['page']) ? (int) $data['page'] : self::DEFAULT_PAGE,
        );
    }
}
