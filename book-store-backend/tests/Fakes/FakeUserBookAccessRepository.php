<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Access\Entities\UserBookAccess;
use App\Domain\Access\Interfaces\UserBookAccessRepositoryInterface;
use PHPUnit\Framework\Assert;

final class FakeUserBookAccessRepository implements UserBookAccessRepositoryInterface
{
    /** @var UserBookAccess[] */
    private array $store = [];

    public function findByUserAndBook(int $userId, int $bookId): ?UserBookAccess
    {
        return $this->store[$this->key($userId, $bookId)] ?? null;
    }

    public function save(UserBookAccess $access): UserBookAccess
    {
        $saved = new UserBookAccess(
            id: $access->id ?? count($this->store) + 1,
            userId: $access->userId,
            bookId: $access->bookId,
            grantedAt: $access->grantedAt,
            stripePaymentIntentId: $access->stripePaymentIntentId,
        );
        $this->store[$this->key($access->userId, $access->bookId)] = $saved;

        return $saved;
    }

    public function hasAccess(int $userId, int $bookId): bool
    {
        return isset($this->store[$this->key($userId, $bookId)]);
    }

    public function assertHasAccess(int $userId, int $bookId): void
    {
        Assert::assertTrue($this->hasAccess($userId, $bookId), "Expected access for user={$userId}, book={$bookId}.");
    }

    public function assertCount(int $expected): void
    {
        Assert::assertCount($expected, $this->store);
    }

    private function key(int $userId, int $bookId): string
    {
        return "{$userId}:{$bookId}";
    }
}
