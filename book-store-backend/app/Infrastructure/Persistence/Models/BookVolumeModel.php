<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class BookVolumeModel extends Model
{
    protected $table = 'book_volumes';

    protected $fillable = [
        'book_id',
        'number',
        'title',
    ];

    protected $casts = [
        'number' => 'integer',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(BookModel::class, 'book_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(BookChapterModel::class, 'volume_id');
    }

    public function scopeByBook(Builder $query, int $bookId): Builder
    {
        return $query->where('book_id', $bookId);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('number');
    }
}
