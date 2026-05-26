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
 * @property int|null $chapter_id
 * @property int|null $page_id
 * @property int $scroll_position
 * @property float $completion_percentage
 * @property bool $is_finished
 * @property DateTimeImmutable|null $last_read_at
 * @property DateTimeImmutable|null $finished_at
 *
 * @method static Builder forUser(int $userId)
 * @method static Builder forBook(int $bookId)
 * @method static Builder finished()
 */
final class UserReadingProgressModel extends Model
{
    protected $table = 'user_reading_progress';

    protected $fillable = [
        'user_id',
        'book_id',
        'chapter_id',
        'page_id',
        'global_page_number',
        'scroll_position',
        'completion_percentage',
        'is_finished',
        'last_read_at',
        'finished_at',
    ];

    protected $casts = [
        'global_page_number' => 'integer',
        'scroll_position' => 'integer',
        'completion_percentage' => 'float',
        'is_finished' => 'boolean',
        'last_read_at' => 'immutable_datetime',
        'finished_at' => 'immutable_datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(BookModel::class, 'book_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BookChapterModel::class, 'chapter_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(BookPageModel::class, 'page_id');
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

    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('is_finished', true);
    }
}
