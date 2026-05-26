<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingList;

use App\Domain\Reading\Enums\ReadingStatusEnum;

final readonly class GetReadingListCommand
{
    private const int DEFAULT_PER_PAGE = 20;

    private const int DEFAULT_PAGE     = 1;

    public function __construct(
        public int                $userId,
        public ?ReadingStatusEnum $status  = null,
        public int                $perPage = self::DEFAULT_PER_PAGE,
        public int                $page    = self::DEFAULT_PAGE,
    ) {}

    public static function fromArray(int $userId, array $data): self
    {
        return new self(
            userId: $userId,
            status: ! empty($data['status']) ? ReadingStatusEnum::from($data['status']) : null,
            perPage: ! empty($data['per_page']) ? (int) $data['per_page'] : self::DEFAULT_PER_PAGE,
            page: ! empty($data['page']) ? (int) $data['page'] : self::DEFAULT_PAGE,
        );
    }
}
