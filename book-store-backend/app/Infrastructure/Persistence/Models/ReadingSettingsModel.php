<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use App\Domain\Reading\Enums\FontFamilyEnum;
use App\Domain\Reading\Enums\LineHeightEnum;
use App\Domain\Reading\Enums\PaginationModeEnum;
use App\Domain\Reading\Enums\ThemeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $font_size
 * @property FontFamilyEnum $font_family
 * @property LineHeightEnum $line_height
 * @property ThemeEnum $theme
 * @property int $page_width
 * @property PaginationModeEnum $pagination_mode
 * @property int $words_per_page
 * @property-read UserModel $user
 */
final class ReadingSettingsModel extends Model
{
    protected $table = 'reading_settings';

    protected $fillable = [
        'user_id',
        'font_size',
        'font_family',
        'line_height',
        'theme',
        'page_width',
        'pagination_mode',
        'words_per_page',
    ];

    protected $casts = [
        'font_family' => FontFamilyEnum::class,
        'line_height' => LineHeightEnum::class,
        'theme' => ThemeEnum::class,
        'pagination_mode' => PaginationModeEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
