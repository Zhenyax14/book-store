<?php

namespace App\Infrastructure\Persistence\Models;

use Database\Factories\BookChapterModelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $book_id
 * @property int|null $volume_id
 * @property int $number
 * @property string $title
 * @property string $slug
 * @property int $reading_time_minutes
 * @property bool $is_published
 * @property BookModel $book
 * @property BookVolumeModel|null $volume
 * @property BookPageModel[] $pages
 */
final class BookChapterModel extends Model
{
    use HasFactory;

    protected $table = 'book_chapters';

    protected $fillable = [
        'book_id',
        'volume_id',
        'number',
        'title',
        'slug',
        'reading_time_minutes',
        'is_published',
    ];

    protected $casts = [
        'number'               => 'integer',
        'reading_time_minutes' => 'integer',
        'is_published'         => 'boolean',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(BookModel::class, 'book_id');
    }

    public function volume(): BelongsTo
    {
        return $this->belongsTo(BookVolumeModel::class, 'volume_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(BookPageModel::class, 'chapter_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeByBook(Builder $query, int $bookId): Builder
    {
        return $query->where('book_id', $bookId);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('number');
    }

    protected static function newFactory(): BookChapterModelFactory
    {
        return BookChapterModelFactory::new();
    }
}
