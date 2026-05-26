<?php

namespace App\Infrastructure\Persistence\Models;

use Database\Factories\BookPageModelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $chapter_id
 * @property int $number
 * @property int $global_number
 * @property string $content
 * @property string $content_format
 * @property int $word_count
 * @property BookChapterModel $chapter
 */
final class BookPageModel extends Model
{
    use HasFactory;

    protected $table = 'book_pages';

    protected $fillable = [
        'chapter_id',
        'number',
        'global_number',
        'content',
        'content_format',
        'word_count',
    ];

    protected $casts = [
        'number'        => 'integer',
        'global_number' => 'integer',
        'word_count'    => 'integer',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BookChapterModel::class, 'chapter_id');
    }

    public function scopeByBook(Builder $query, int $bookId): Builder
    {
        return $query->whereHas(
            'chapter',
            fn(BelongsTo $q) => $q->where('book_id', $bookId),
        );
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('number');
    }

    public function scopeByGlobalNumber(Builder $query, int $globalNumber): Builder
    {
        return $query->where('global_number', $globalNumber);
    }

    protected static function newFactory(): BookPageModelFactory
    {
        return BookPageModelFactory::new();
    }
}
