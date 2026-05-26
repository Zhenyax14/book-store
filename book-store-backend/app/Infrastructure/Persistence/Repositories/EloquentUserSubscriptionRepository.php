<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Access\Entities\UserSubscription;
use App\Domain\Access\Interfaces\UserSubscriptionAccessRepositoryInterface;
use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\Exceptions\SubscriptionNotFoundException;
use App\Domain\Subscription\Interfaces\UserSubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObjects\SubscriptionCollection;
use App\Domain\Subscription\ValueObjects\SubscriptionFilter;
use App\Infrastructure\Persistence\Models\UserSubscriptionModel;

final class EloquentUserSubscriptionRepository implements UserSubscriptionAccessRepositoryInterface, UserSubscriptionRepositoryInterface
{
    public function findActiveByUser(int $userId): ?UserSubscription
    {
        $model = UserSubscriptionModel::query()
            ->forUser($userId)
            ->active()
            ->latest('started_at')
            ->first();

        return $model ? $this->toAccessDomain($model) : null;
    }

    public function saveAccess(UserSubscription $subscription): UserSubscription
    {
        if (null === $subscription->id) {
            $model = UserSubscriptionModel::query()->create($this->toArray($subscription));
        } else {
            $model = UserSubscriptionModel::query()->findOrFail($subscription->id);
            $model->update($this->toArray($subscription));
        }

        return $this->toAccessDomain($model);
    }

    public function findById(int $id): Subscription
    {
        $subscription = UserSubscriptionModel::query()->find($id);
        if (null === $subscription) {
            throw new SubscriptionNotFoundException($id);
        }

        return $this->toSubscriptionDomain(UserSubscriptionModel::query()->findOrFail($id));
    }

    public function findAll(SubscriptionFilter $filter): SubscriptionCollection
    {
        $query = UserSubscriptionModel::query();

        if ($filter->status) {
            $query = $query->where('status', $filter->status);
        }

        if ($filter->search) {
            $query = $query
                ->where('stripe_subscription_id', 'like', "%{$filter->search}%");
        }

        $paginator = $query->paginate(
            perPage: $filter->perPage,
            page: $filter->page,
        );

        return new SubscriptionCollection(
            items: array_map(
                fn(UserSubscriptionModel $model) => $this->toSubscriptionDomain($model),
                $paginator->items(),
            ),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
        );
    }

    private function toAccessDomain(UserSubscriptionModel $model): UserSubscription
    {
        return new UserSubscription(
            id: $model->id,
            userId: $model->user_id,
            status: $model->status,
            startedAt: $model->started_at,
            expiresAt: $model->expires_at,
            stripeSubscriptionId: $model->stripe_subscription_id,
        );
    }

    private function toArray(UserSubscription $subscription): array
    {
        return [
            'user_id'                => $subscription->userId,
            'status'                 => $subscription->status->value,
            'stripe_subscription_id' => $subscription->stripeSubscriptionId,
            'started_at'             => $subscription->startedAt,
            'expires_at'             => $subscription->expiresAt,
        ];
    }

    private function toSubscriptionDomain(UserSubscriptionModel $model): Subscription
    {
        return new Subscription(
            id: $model->id,
            userId: $model->user_id,
            status: $model->status,
            stripeSubscriptionId: $model->stripe_subscription_id,
            startedAt: $model->started_at,
            expiresAt: $model->expires_at,
        );
    }
}
