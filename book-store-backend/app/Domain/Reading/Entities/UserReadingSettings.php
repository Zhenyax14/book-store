<?php

declare(strict_types=1);

namespace App\Domain\Reading\Entities;

use App\Domain\Reading\Enums\FontFamilyEnum;
use App\Domain\Reading\Enums\LineHeightEnum;
use App\Domain\Reading\Enums\PaginationModeEnum;
use App\Domain\Reading\Enums\ThemeEnum;
use DateTimeImmutable;

final readonly class UserReadingSettings
{
    public function __construct(
        public ?int               $id = null,
        public int                $userId,
        public int                $fontSize,
        public FontFamilyEnum     $fontFamily,
        public LineHeightEnum     $lineHeight,
        public ThemeEnum          $theme,
        public int                $pageWidth,
        public PaginationModeEnum $paginationMode,
        public int                $wordsPerPage,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null,
    ) {}

    public static function defaults(int $userId): self
    {
        $now = new DateTimeImmutable();

        return new self(
            id: null,
            userId: $userId,
            fontSize: 16,
            fontFamily: FontFamilyEnum::GEORGIA,
            lineHeight: LineHeightEnum::NORMAL,
            theme: ThemeEnum::LIGHT,
            pageWidth: 70,
            paginationMode: PaginationModeEnum::PAGE,
            wordsPerPage: 300,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id'         => $this->userId,
            'font_size'       => $this->fontSize,
            'font_family'     => $this->fontFamily,
            'line_height'     => $this->lineHeight,
            'theme'           => $this->theme,
            'page_width'      => $this->pageWidth,
            'pagination_mode' => $this->paginationMode,
            'words_per_page'  => $this->wordsPerPage,
            'created_at'      => $this->createdAt,
            'updated_at'      => $this->updatedAt,
        ];
    }
}
