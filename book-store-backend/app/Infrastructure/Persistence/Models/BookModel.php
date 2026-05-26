<?php

namespace App\Infrastructure\Persistence\Models;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use Database\Factories\BookModelFactory;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $isbn
 * @property string $language
 * @property string $publisher
 * @property int $published_year
 * @property int $edition
 * @property int $pages_count
 * @property string $cover_path
 * @property AccessTypeEnum $access_type
 * @property int $price
 * @property string $currency
 * @property BookStatusEnum $status
 * @property DateTimeImmutable|null $published_at
 * @property string|null $file_path
 *
 * @property Collection<BookChapterModel> $chapters
 * @property Collection<BookVolumeModel> $volumes
 * @property Collection<TagModel> $tags
 *
 * @method static Builder published()
 * @method static Builder draft()
 * @method static Builder archived()
 * @method static Builder byAccessType(AccessTypeEnum $accessType)
 * @method static Builder byLanguage(string $language)
 * @method static Builder free()
 */
final class BookModel extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'isbn',
        'language',
        'publisher',
        'published_year',
        'edition',
        'pages_count',
        'cover_path',
        'access_type',
        'price',
        'currency',
        'status',
        'published_at',
        'file_path',
    ];

    protected $casts = [
        'price'        => 'integer',
        'pages_count'  => 'integer',
        'published_at' => 'immutable_datetime',
        'status' => BookStatusEnum::class,
        'access_type' => AccessTypeEnum::class,
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(BookChapterModel::class, 'book_id');
    }

    public function volumes(): HasMany
    {
        return $this->hasMany(BookVolumeModel::class, 'book_id');
    }

    public function bookmark(): HasMany
    {
        return $this->hasMany(BookmarkModel::class, 'book_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', BookStatusEnum::PUBLISHED)
            ->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', BookStatusEnum::DRAFT);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', BookStatusEnum::ARCHIVED);
    }

    public function scopeByAccessType(Builder $query, AccessTypeEnum $accessType): Builder
    {
        return $query->where('access_type', $accessType);
    }

    public function scopeByLanguage(Builder $query, string $language): Builder
    {
        return $query->where('language', $language);
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('access_type', AccessTypeEnum::FREE->value);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            TagModel::class,
            'book_tag',
            'book_id',
            'tag_id',
        );
    }

    protected static function newFactory(): BookModelFactory
    {
        return BookModelFactory::new();
    }
}
