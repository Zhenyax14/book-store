<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $user_id
 * @property int         $book_id
 * @property string|null $stripe_payment_intent_id
 * @property CarbonImmutable $granted_at
 *
 * @method static Builder forUser(int $userId)
 * @method static Builder forBook(int $bookId)
 */
final class UserBookAccessModel extends Model
{
    protected $table = 'user_book_access';

    protected $fillable = [
        'user_id',
        'book_id',
        'stripe_payment_intent_id',
        'granted_at',
    ];

    protected $casts = [
        'granted_at' => 'immutable_datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(BookModel::class, 'book_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForBook(Builder $query, int $bookId): Builder
    {
        return $query->where('book_id', $bookId);
    }
}
