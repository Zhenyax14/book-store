<?php

namespace App\Infrastructure\Persistence\Models;

use App\Domain\Reading\Enums\ReadingStatusEnum;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property ReadingStatusEnum $status
 * @property int $current_page
 * @property int|null $total_pages
 * @property DateTimeImmutable|null $started_at
 * @property DateTimeImmutable|null $finished_at
 */
final class ReadingEntryModel extends Model
{
    protected $table = 'user_reading_list';

    protected $fillable = [
        'user_id', 'book_id', 'status',
        'current_page', 'total_pages',
        'started_at', 'finished_at',
    ];

    protected $casts = [
        'status'      => ReadingStatusEnum::class,
        'started_at'  => 'immutable_datetime',
        'finished_at' => 'immutable_datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(BookModel::class, 'book_id');
    }

    public function progressPercentage(): ?float
    {
        if (null === $this->total_pages || 0 === $this->total_pages) {
            return null;
        }

        return round(($this->current_page / $this->total_pages) * 100, 2);
    }
}
