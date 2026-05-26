<?php

declare(strict_types=1);

namespace App\Application\Notification\UseCases\GetUnreadCount;

final readonly class GetUnreadCountCommand
{
    public function __construct(
        public int $userId,
    ) {}
}
