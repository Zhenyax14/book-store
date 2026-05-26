<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Catalog\Entities;

use App\Domain\Catalog\Entities\Book;
use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class BookTest extends TestCase
{
    public const string DEFAULT_CURRENCY = 'EUR';

    public function test_draft_book_is_not_published(): void
    {
        $book = $this->makeBook(['status' => BookStatusEnum::DRAFT]);

        $this->assertFalse($book->isPublished());
    }

    public function test_published_book_with_past_date_is_published(): void
    {
        $book = $this->makeBook([
            'status'      => BookStatusEnum::PUBLISHED,
            'publishedAt' => new DateTimeImmutable('-1 day'),
        ]);

        $this->assertTrue($book->isPublished());
    }

    public function test_published_book_with_future_date_is_not_published(): void
    {
        $book = $this->makeBook([
            'status'      => BookStatusEnum::PUBLISHED,
            'publishedAt' => new DateTimeImmutable('+1 day'),
        ]);

        $this->assertFalse($book->isPublished());
    }

    public function test_free_access_type_book_is_free(): void
    {
        $book = $this->makeBook(['accessType' => AccessTypeEnum::FREE]);

        $this->assertTrue($book->isFree());
    }

    public function test_book_with_zero_price_is_free(): void
    {
        $book = $this->makeBook([
            'accessType' => AccessTypeEnum::PURCHASE,
            'price'      => Money::zero(new Currency(self::DEFAULT_CURRENCY)),
        ]);

        $this->assertTrue($book->isFree());
    }

    public function test_subscription_book_with_price_is_not_free(): void
    {
        $book = $this->makeBook([
            'accessType' => AccessTypeEnum::SUBSCRIPTION,
            'price'      => Money::ofEur(9900),
        ]);

        $this->assertFalse($book->isFree());
    }

    public function test_publish_changes_status_to_published(): void
    {
        $book      = $this->makeBook(['status' => BookStatusEnum::DRAFT]);
        $published = $book->publish();

        $this->assertEquals(BookStatusEnum::PUBLISHED, $published->status);
    }

    public function test_publish_sets_published_at(): void
    {
        $book      = $this->makeBook(['status' => BookStatusEnum::DRAFT]);
        $published = $book->publish();

        $this->assertNotNull($published->publishedAt);
        $this->assertInstanceOf(DateTimeImmutable::class, $published->publishedAt);
    }

    public function test_publish_returns_new_instance(): void
    {
        $book      = $this->makeBook(['status' => BookStatusEnum::DRAFT]);
        $published = $book->publish();

        $this->assertEquals(BookStatusEnum::DRAFT, $book->status);
        $this->assertNotSame($book, $published);
    }

    private function makeBook(array $overrides = []): Book
    {
        return new Book(
            title: $overrides['title']         ?? 'Master and Margarita',
            slug: $overrides['slug']          ?? 'master-i-margarita',
            language: $overrides['language']      ?? 'ru',
            edition: $overrides['edition']       ?? 1,
            pagesCount: $overrides['pagesCount']    ?? 0,
            accessType: $overrides['accessType']    ?? AccessTypeEnum::FREE,
            price: $overrides['price']         ?? Money::zero(new Currency(self::DEFAULT_CURRENCY)),
            status: $overrides['status']        ?? BookStatusEnum::DRAFT,
            description: $overrides['description']   ?? null,
            isbn: $overrides['isbn']          ?? null,
            publishedAt: $overrides['publishedAt']   ?? null,
            coverPath: $overrides['coverPath']     ?? null,
            filePath: $overrides['filePath']      ?? null,
            publisher: $overrides['publisher']     ?? null,
            publishedYear: $overrides['publishedYear'] ?? null,
            id: $overrides['id']            ?? null,
        );
    }
}
