<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\UpdateBook;

use App\Domain\Catalog\Enums\AccessTypeEnum;

final readonly class UpdateBookCommand
{
    public function __construct(
        public int     $id,
        public string  $title,
        public ?string $description = null,
        public ?string $isbn = null,
        public string  $language,
        public ?string $publisher = null,
        public ?int    $publishedYear = null,
        public AccessTypeEnum  $accessType,
        public int     $price,
        public string  $currency,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'],
            description: $data['description'] ?? null,
            isbn: $data['isbn'] ?? null,
            language: $data['language'],
            publisher: $data['publisher'] ?? null,
            publishedYear: $data['published_year'] ?? null,
            accessType: AccessTypeEnum::from($data['access_type']),
            price: $data['price'],
            currency: $data['currency'],
        );
    }
}
