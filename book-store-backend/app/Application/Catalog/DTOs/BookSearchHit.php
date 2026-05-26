<?php

declare(strict_types=1);

namespace App\Application\Catalog\DTOs;

/**
 * @property int $bookId
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $accessType
 * @property string $status
 * @property float $rankingScore
 */
final readonly class BookSearchHit
{
    public function __construct(
        public int     $bookId,
        public string  $title,
        public string  $slug,
        public ?string $description,
        public string  $accessType,
        public string  $status,
        public float   $rankingScore,
    ) {}
}
