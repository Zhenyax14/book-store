<?php

declare(strict_types=1);

namespace App\Application\Catalog\DTOs;

final readonly class ParsedChapter
{
    public function __construct(
        public string $title,
        public int    $number,
        /** @var ParsedPage[] */
        public array  $pages,
    ) {}
}
