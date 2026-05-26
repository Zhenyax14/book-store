<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Models\TagModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    private const array TAGS = [
        'Novel',
        'Novella',
        'Short Story',
        'Poetry',
        'Drama',
        'Science Fiction',
        'Fantasy',
        'Detective',
        'Thriller',
        'Horror',
        'Adventure',
        'Historical Fiction',
        'Biography',
        'Autobiography',
        'Memoir',

        'Classic',
        'Contemporary Fiction',
        'Foreign Literature',
        'Russian Literature',
        'Philosophy',
        'Psychology',
        'Self-Development',
        'Business',
        'Science',
        'History',
        'Politics',
        'Religion',
        'Art',

        'Children',
        'Young Adult',
        'Adult',

        'Short Stories',
        'Anthology',
        'Series',
    ];

    public function run(): void
    {
        foreach (self::TAGS as $name) {
            TagModel::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }

        $this->command->info('Tags seeded: ' . count(self::TAGS));
    }
}
