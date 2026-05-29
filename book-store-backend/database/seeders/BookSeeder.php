<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\TagModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Random\RandomException;

final class BookSeeder extends Seeder
{
    /**
     * Well-known classic books — always present, deterministic slugs,
     * useful for manual testing and demos.
     */
    private const array CLASSIC_BOOKS = [
        [
            'title'          => 'The Master and Margarita',
            'description'    => 'A novel by Mikhail Bulgakov, weaving together a satirical story of the Devil visiting Soviet Moscow with a retelling of the story of Pontius Pilate.',
            'isbn'           => '978-0-14-028049-8',
            'language'       => 'en',
            'publisher'      => 'Penguin Classics',
            'published_year' => 1967,
            'access_type'    => AccessTypeEnum::FREE,
            'price'          => 0,
            'currency'       => 'EUR',
            'status'         => BookStatusEnum::PUBLISHED,
            'tags'           => ['Novel', 'Classic', 'Russian Literature', 'Fantasy'],
        ],
        [
            'title'          => 'Crime and Punishment',
            'description'    => 'Dostoevsky\'s psychological drama following a student who commits a murder and grapples with guilt and redemption in 19th-century St. Petersburg.',
            'isbn'           => '978-0-14-044913-6',
            'language'       => 'en',
            'publisher'      => 'Penguin Classics',
            'published_year' => 1866,
            'access_type'    => AccessTypeEnum::FREE,
            'price'          => 0,
            'currency'       => 'EUR',
            'status'         => BookStatusEnum::PUBLISHED,
            'tags'           => ['Novel', 'Classic', 'Russian Literature', 'Psychology'],
        ],
        [
            'title'          => 'Clean Code',
            'description'    => 'A handbook of agile software craftsmanship by Robert C. Martin. Packed with practical advice on writing readable, maintainable code.',
            'isbn'           => '978-0-13-235088-4',
            'language'       => 'en',
            'publisher'      => 'Prentice Hall',
            'published_year' => 2008,
            'access_type'    => AccessTypeEnum::PURCHASE,
            'price'          => 2990,
            'currency'       => 'EUR',
            'status'         => BookStatusEnum::PUBLISHED,
            'tags'           => ['Science', 'Business', 'Self-Development'],
        ],
        [
            'title'          => 'Domain-Driven Design',
            'description'    => 'Eric Evans\' seminal work on tackling complexity in the heart of software, introducing bounded contexts, aggregates, and ubiquitous language.',
            'isbn'           => '978-0-32-112521-7',
            'language'       => 'en',
            'publisher'      => 'Addison-Wesley',
            'published_year' => 2003,
            'access_type'    => AccessTypeEnum::PURCHASE,
            'price'          => 3490,
            'currency'       => 'EUR',
            'status'         => BookStatusEnum::PUBLISHED,
            'tags'           => ['Science', 'Business'],
        ],
        [
            'title'          => 'The Great Gatsby',
            'description'    => 'F. Scott Fitzgerald\'s masterpiece about the American dream, wealth, and love set in the Jazz Age.',
            'isbn'           => '978-0-74-327356-5',
            'language'       => 'en',
            'publisher'      => 'Scribner',
            'published_year' => 1925,
            'access_type'    => AccessTypeEnum::SUBSCRIPTION,
            'price'          => 990,
            'currency'       => 'EUR',
            'status'         => BookStatusEnum::PUBLISHED,
            'tags'           => ['Novel', 'Classic', 'Contemporary Fiction'],
        ],
        [
            'title'          => '1984',
            'description'    => 'George Orwell\'s dystopian social science fiction novel and cautionary tale about totalitarianism, surveillance, and the manipulation of truth.',
            'isbn'           => '978-0-45-152493-5',
            'language'       => 'en',
            'publisher'      => 'Secker & Warburg',
            'published_year' => 1949,
            'access_type'    => AccessTypeEnum::SUBSCRIPTION,
            'price'          => 990,
            'currency'       => 'EUR',
            'status'         => BookStatusEnum::PUBLISHED,
            'tags'           => ['Novel', 'Classic', 'Science Fiction'],
        ],
        [
            'title'          => 'Dune',
            'description'    => 'Frank Herbert\'s epic science fiction saga set in a distant future, exploring politics, religion, ecology, and human nature on the desert planet Arrakis.',
            'isbn'           => '978-0-44-100590-2',
            'language'       => 'en',
            'publisher'      => 'Chilton Books',
            'published_year' => 1965,
            'access_type'    => AccessTypeEnum::SUBSCRIPTION,
            'price'          => 990,
            'currency'       => 'EUR',
            'status'         => BookStatusEnum::PUBLISHED,
            'tags'           => ['Novel', 'Science Fiction', 'Series'],
        ],
        [
            'title'          => 'Sapiens: A Brief History of Humankind',
            'description'    => 'Yuval Noah Harari\'s sweeping narrative of the entire history of our species, from prehistoric man to the modern age.',
            'isbn'           => '978-0-06-231609-7',
            'language'       => 'en',
            'publisher'      => 'Harper',
            'published_year' => 2011,
            'access_type'    => AccessTypeEnum::PURCHASE,
            'price'          => 1990,
            'currency'       => 'EUR',
            'status'         => BookStatusEnum::PUBLISHED,
            'tags'           => ['History', 'Science', 'Contemporary Fiction'],
        ],
        // Draft book — for testing admin workflows
        [
            'title'          => 'Upcoming PHP Architecture Guide',
            'description'    => 'An upcoming guide to modern PHP application architecture with Clean Architecture and DDD.',
            'isbn'           => null,
            'language'       => 'en',
            'publisher'      => 'BookStore Press',
            'published_year' => null,
            'access_type'    => AccessTypeEnum::PURCHASE,
            'price'          => 2490,
            'currency'       => 'EUR',
            'status'         => BookStatusEnum::DRAFT,
            'tags'           => ['Science', 'Business'],
        ],
    ];

    private const int FAKER_PUBLISHED_COUNT = 15;

    private const int FAKER_DRAFT_COUNT     = 5;

    public function run(): void
    {
        $this->seedClassics();
        $this->seedFakerBooks();

        $total = count(self::CLASSIC_BOOKS) + self::FAKER_PUBLISHED_COUNT + self::FAKER_DRAFT_COUNT;
        $this->command->info("Books seeded: {$total}");
    }

    /**
     * @throws RandomException
     */
    private function seedClassics(): void
    {
        $tagMap = TagModel::query()
            ->pluck('id', 'name')
            ->all();

        foreach (self::CLASSIC_BOOKS as $data) {
            $slug = Str::slug($data['title']);

            /** @var BookModel $book */
            $book = BookModel::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'title'          => $data['title'],
                    'slug'           => $slug,
                    'description'    => $data['description'],
                    'isbn'           => $data['isbn'],
                    'language'       => $data['language'],
                    'publisher'      => $data['publisher'],
                    'published_year' => $data['published_year'],
                    'edition'        => 1,
                    'pages_count'    => 0,
                    'access_type'    => $data['access_type'],
                    'price'          => $data['price'],
                    'currency'       => $data['currency'],
                    'status'         => $data['status'],
                    'published_at'   => BookStatusEnum::PUBLISHED === $data['status']
                        ? now()->subDays(random_int(10, 365))
                        : null,
                ],
            );

            // Attach tags
            $tagIds = array_filter(
                array_map(static fn(string $name) => $tagMap[$name] ?? null, $data['tags']),
            );

            if ([] !== $tagIds) {
                $book->tags()->syncWithoutDetaching(array_values($tagIds));
            }
        }
    }

    private function seedFakerBooks(): void
    {
        $allTagIds = TagModel::query()->pluck('id')->all();

        BookModel::factory()
            ->count(self::FAKER_PUBLISHED_COUNT)
            ->published()
            ->create()
            ->each(/**
             * @throws RandomException
             */ function (BookModel $book) use ($allTagIds): void {
                $randomTagIds = array_rand(
                    array_flip($allTagIds),
                    min(random_int(1, 3), count($allTagIds)),
                );

                $book->tags()->syncWithoutDetaching(
                    is_array($randomTagIds) ? $randomTagIds : [$randomTagIds],
                );
            });

        BookModel::factory()
            ->count(self::FAKER_DRAFT_COUNT)
            ->create(['status' => BookStatusEnum::DRAFT]);
    }
}
