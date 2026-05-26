<?php

declare(strict_types=1);

namespace App\Application\Catalog\DTOs;

use App\Domain\Reading\Enums\ContentFormatEnum;

final readonly class ParsedPage
{
    public function __construct(
        public int               $number,
        public string            $content,
        public ContentFormatEnum $contentFormat,
        public int               $wordCount,
    ) {}
}
