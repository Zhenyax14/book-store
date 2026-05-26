<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\UpdateReadingSettings;

use App\Domain\Reading\Enums\FontFamilyEnum;
use App\Domain\Reading\Enums\LineHeightEnum;
use App\Domain\Reading\Enums\PaginationModeEnum;
use App\Domain\Reading\Enums\ThemeEnum;

final readonly class UpdateReadingSettingsCommand
{
    public function __construct(
        public int $userId,
        public int $fontSize,
        public FontFamilyEnum $fontFamily,
        public LineHeightEnum $lineHeight,
        public ThemeEnum $theme,
        public int $pageWidth,
        public PaginationModeEnum $paginationMode,
        public int $wordsPerPage,
    ) {}
}
