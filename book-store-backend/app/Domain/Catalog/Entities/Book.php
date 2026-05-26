<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Entities;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use App\Domain\Shared\ValueObjects\Money;
use DateTimeImmutable;

final readonly class Book
{
    public function __construct(
        public string             $title,
        public string             $slug,
        public string             $language,
        public int                $edition,
        public int                $pagesCount,
        public AccessTypeEnum     $accessType,
        public Money              $price,
        public BookStatusEnum     $status,
        public ?string            $description = null,
        public ?string            $isbn = null,
        public ?DateTimeImmutable $publishedAt = null,
        public ?string            $coverPath = null,
        public ?string            $filePath = null,
        public ?string            $publisher = null,
        public ?int               $publishedYear = null,
        public ?int               $id = null,
    ) {}

    public function isPublished(): bool
    {
        return BookStatusEnum::PUBLISHED === $this->status
            && $this->publishedAt <= new DateTimeImmutable();
    }

    public function isFree(): bool
    {
        return AccessTypeEnum::FREE === $this->accessType
            || 0 === $this->price->amount;
    }

    public function publish(): self
    {
        return new self(
            title: $this->title,
            slug: $this->slug,
            language: $this->language,
            edition: $this->edition,
            pagesCount: $this->pagesCount,
            accessType: $this->accessType,
            price: $this->price,
            status: BookStatusEnum::PUBLISHED,
            description: $this->description,
            isbn: $this->isbn,
            publishedAt: new DateTimeImmutable(),
            coverPath: $this->coverPath,
            filePath: $this->filePath,
            publisher: $this->publisher,
            publishedYear: $this->publishedYear,
            id: $this->id,
        );
    }

    public function withPagesCount(int $pagesCount): self
    {
        return new self(
            title: $this->title,
            slug: $this->slug,
            language: $this->language,
            edition: $this->edition,
            pagesCount: $pagesCount,
            accessType: $this->accessType,
            price: $this->price,
            status: $this->status,
            description: $this->description,
            isbn: $this->isbn,
            publishedAt: $this->publishedAt,
            coverPath: $this->coverPath,
            publisher: $this->publisher,
            publishedYear: $this->publishedYear,
            id: $this->id,
        );
    }

    public function withFilePath(string $filePath): self
    {
        return new self(
            title: $this->title,
            slug: $this->slug,
            language: $this->language,
            edition: $this->edition,
            pagesCount: $this->pagesCount,
            accessType: $this->accessType,
            price: $this->price,
            status: $this->status,
            description: $this->description,
            isbn: $this->isbn,
            publishedAt: $this->publishedAt,
            coverPath: $this->coverPath,
            filePath: $filePath,
            publisher: $this->publisher,
            publishedYear: $this->publishedYear,
            id: $this->id,
        );
    }
}
