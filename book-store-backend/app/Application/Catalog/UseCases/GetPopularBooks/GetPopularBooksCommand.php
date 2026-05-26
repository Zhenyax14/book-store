<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\GetPopularBooks;

use App\Domain\Catalog\Enums\PopularityPeriodEnum;

final readonly class GetPopularBooksCommand
{
    public function __construct(
        public PopularityPeriodEnum $period,
        public int                  $perPage = 20,
        public int                  $page = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            period: PopularityPeriodEnum::from($data['period'] ?? 'month'),
            perPage: (int) ($data['per_page'] ?? 20),
            page: (int) ($data['page'] ?? 1),
        );
    }
}
