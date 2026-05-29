<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\CreateBook;

use App\Domain\Catalog\Enums\AccessTypeEnum;

final readonly class CreateBookCommand
{
    public function __construct(
        public string         $title,
        public string         $language,
        public AccessTypeEnum $accessType,
        public int            $price,
        public string         $currency,
        public ?string         $description = null,
        public ?string        $isbn = null,
        public ?string        $publisher = null,
        public ?int           $publishedYear = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            language: $data['language'],
            accessType: AccessTypeEnum::from($data['access_type']),
            price: $data['price'],
            currency: $data['currency'],
            description: $data['description'] ?? null,
            isbn: $data['isbn'] ?? null,
            publisher: $data['publisher'] ?? null,
            publishedYear: $data['published_year'] ?? null,
        );
    }
}
