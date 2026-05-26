<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @property string      $id
 * @property string      $type
 * @property string      $notifiable_type
 * @property int         $notifiable_id
 * @property array       $data
 * @property Carbon|null $read_at
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 *
 * @method static Builder forUser(int $userId)
 * @method static Builder unread()
 * @method static Builder recentFirst()
 */
final class NotificationModel extends DatabaseNotification
{
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query
            ->where('notifiable_type', UserModel::class)
            ->where('notifiable_id', $userId);
    }

    public function scopeRecentFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }
}
