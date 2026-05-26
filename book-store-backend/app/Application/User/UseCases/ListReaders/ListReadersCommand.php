<?php

declare(strict_types=1);

namespace App\Application\User\UseCases\ListReaders;

use App\Domain\User\Enums\ReaderFilterEnum;

final readonly class ListReadersCommand
{
    private const int DEFAULT_PER_PAGE = 20;

    private const int DEFAULT_PAGE = 1;

    public function __construct(
        public ?ReaderFilterEnum $filter = null,
        public ?string           $search = null,
        public ?int              $perPage = null,
        public ?int              $page = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            filter: ! empty($data['filter']) ? ReaderFilterEnum::from($data['filter']) : null,
            search: $data['search'] ?? null,
            perPage: ! empty($data['per_page']) ? (int) $data['per_page'] : self::DEFAULT_PER_PAGE,
            page: ! empty($data['page']) ? (int) $data['page'] : self::DEFAULT_PAGE,
        );
    }
}
