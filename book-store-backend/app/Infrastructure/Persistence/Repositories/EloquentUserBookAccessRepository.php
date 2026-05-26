<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Access\Entities\UserBookAccess;
use App\Domain\Access\Interfaces\UserBookAccessRepositoryInterface;
use App\Infrastructure\Persistence\Models\UserBookAccessModel;

final class EloquentUserBookAccessRepository implements UserBookAccessRepositoryInterface
{
    public function findByUserAndBook(int $userId, int $bookId): ?UserBookAccess
    {
        $model = UserBookAccessModel::query()
            ->forUser($userId)
            ->forBook($bookId)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function save(UserBookAccess $access): UserBookAccess
    {
        $model = UserBookAccessModel::query()->updateOrCreate(
            [
                'user_id' => $access->userId,
                'book_id' => $access->bookId,
            ],
            [
                'stripe_payment_intent_id' => $access->stripePaymentIntentId,
                'granted_at'               => $access->grantedAt,
            ],
        );

        return $this->toDomain($model);
    }

    public function hasAccess(int $userId, int $bookId): bool
    {
        return UserBookAccessModel::query()
            ->forUser($userId)
            ->forBook($bookId)
            ->exists();
    }

    private function toDomain(UserBookAccessModel $model): UserBookAccess
    {
        return new UserBookAccess(
            id: $model->id,
            userId: $model->user_id,
            bookId: $model->book_id,
            grantedAt: $model->granted_at,
            stripePaymentIntentId: $model->stripe_payment_intent_id,
        );
    }
}
