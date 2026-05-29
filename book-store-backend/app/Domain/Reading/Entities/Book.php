<?php

declare(strict_types=1);

namespace App\Domain\Reading\Entities;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $publisher
 * @property BookChapter[] $chapters
 * @property Bookmark|null $bookmark
 */
final readonly class Book
{
    public function __construct(
        public int       $id,
        public string    $title,
        public string    $slug,
        public string    $description,
        public ?string   $publisher = null,
        public array     $chapters,
        public ?Bookmark $bookmark = null,
    ) {}
}
