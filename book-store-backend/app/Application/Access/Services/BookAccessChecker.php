<?php

declare(strict_types=1);

namespace App\Application\Access\Services;

use App\Domain\Access\Interfaces\BookAccessCheckerInterface;
use App\Domain\Access\Interfaces\UserBookAccessRepositoryInterface;
use App\Domain\Access\Interfaces\UserSubscriptionAccessRepositoryInterface;
use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;

final readonly class BookAccessChecker implements BookAccessCheckerInterface
{
    public function __construct(
        private BookRepositoryInterface                   $books,
        private UserBookAccessRepositoryInterface         $bookAccess,
        private UserSubscriptionAccessRepositoryInterface $subscriptions,
    ) {}

    public function canRead(int $userId, int $bookId): bool
    {
        $book = $this->books->findById($bookId);

        if (null === $book) {
            return false;
        }

        return match ($book->accessType) {
            AccessTypeEnum::FREE         => true,
            AccessTypeEnum::PURCHASE     => $this->bookAccess->hasAccess($userId, $bookId),
            AccessTypeEnum::SUBSCRIPTION => $this->hasActiveSubscription($userId)
                || $this->bookAccess->hasAccess($userId, $bookId),
        };
    }

    private function hasActiveSubscription(int $userId): bool
    {
        return $this->subscriptions->findActiveByUser($userId)?->isActive() ?? false;
    }
}
