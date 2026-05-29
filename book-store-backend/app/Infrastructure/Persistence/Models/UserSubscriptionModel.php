<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use App\Domain\Shared\Enums\SubscriptionStatusEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                      $id
 * @property int                      $user_id
 * @property SubscriptionStatusEnum   $status
 * @property string|null              $stripe_subscription_id
 * @property CarbonImmutable  $started_at
 * @property CarbonImmutable  $expires_at
 *
 * @method static Builder forUser(int $userId)
 * @method static Builder active()
 */
final class UserSubscriptionModel extends Model
{
    protected $table = 'user_subscriptions';

    protected $fillable = [
        'user_id',
        'status',
        'stripe_subscription_id',
        'started_at',
        'expires_at',
    ];

    protected $casts = [
        'status'     => SubscriptionStatusEnum::class,
        'started_at' => 'immutable_datetime',
        'expires_at' => 'immutable_datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', SubscriptionStatusEnum::ACTIVE)
            ->where('expires_at', '>', now());
    }
}
