<?php

declare(strict_types=1);

namespace App\Application\Notification\UseCases\GetNotifications;

final readonly class GetNotificationsCommand
{
    private const int DEFAULT_PER_PAGE = 20;

    private const int DEFAULT_PAGE = 1;

    public function __construct(
        public int  $userId,
        public int $perPage = self::DEFAULT_PER_PAGE,
        public int $page = self::DEFAULT_PAGE,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            perPage: ! empty($data['per_page']) ? (int) $data['per_page'] : self::DEFAULT_PER_PAGE,
            page: ! empty($data['page']) ? (int) $data['page'] : self::DEFAULT_PAGE,
        );
    }
}
