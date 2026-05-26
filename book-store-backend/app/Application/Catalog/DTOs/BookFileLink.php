<?php

declare(strict_types=1);

namespace App\Application\Catalog\DTOs;

final readonly class BookFileLink
{
    public function __construct(
        public string $mimeType,
        public string $url,
        public string $label,
    ) {}
}
