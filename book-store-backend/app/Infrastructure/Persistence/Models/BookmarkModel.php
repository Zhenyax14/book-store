<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int $chapter_id
 * @property int $page_id
 * @property string $label
 * @property string $color
 */
class BookmarkModel extends Model
{
    protected $table = 'bookmarks';

    protected $fillable = [
        'user_id', 'book_id',
        'chapter_id',
        'page_id', 'label', 'color',
    ];

    protected $casts = [
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(BookModel::class, 'book_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(BookPageModel::class, 'page_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BookChapterModel::class, 'chapter_id');
    }
}
