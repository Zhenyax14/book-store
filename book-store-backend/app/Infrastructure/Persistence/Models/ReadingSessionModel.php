<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int|null $start_page_id
 * @property int|null $end_page_id
 * @property DateTimeImmutable $started_at
 * @property DateTimeImmutable|null $ended_at
 * @property int $pages_read
 * @property int $duration_seconds
 *
 * @method static Builder forUser(int $userId)
 * @method static Builder forBook(int $bookId)
 * @method static Builder active()
 * @method static Builder recentFirst()
 */
final class ReadingSessionModel extends Model
{
    protected $table = 'reading_sessions';

    protected $fillable = [
        'user_id',
        'book_id',
        'start_page_id',
        'end_page_id',
        'started_at',
        'ended_at',
        'pages_read',
        'duration_seconds',
    ];

    protected $casts = [
        'pages_read' => 'integer',
        'duration_seconds' => 'integer',
        'started_at' => 'immutable_datetime',
        'ended_at' => 'immutable_datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(BookModel::class, 'book_id');
    }

    public function startPage(): BelongsTo
    {
        return $this->belongsTo(BookPageModel::class, 'start_page_id');
    }

    public function endPage(): BelongsTo
    {
        return $this->belongsTo(BookPageModel::class, 'end_page_id');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForBook(Builder $query, int $bookId): Builder
    {
        return $query->where('book_id', $bookId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('ended_at');
    }

    public function scopeRecentFirst(Builder $query): Builder
    {
        return $query->orderByDesc('started_at');
    }
}
