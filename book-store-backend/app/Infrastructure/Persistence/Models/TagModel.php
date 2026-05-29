<?php

namespace App\Infrastructure\Persistence\Models;

use Database\Factories\TagModelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property-read Collection<BookModel> $books
 * @method static Builder bySlug(string $slug)
 */
final class TagModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table    = 'tags';

    protected $fillable = ['name', 'slug'];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(
            BookModel::class,
            'book_tag',
            'tag_id',
            'book_id',
        );
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    protected static function newFactory(): TagModelFactory
    {
        return TagModelFactory::new();
    }
}
