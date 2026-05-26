<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Reading\Enums\ContentFormatEnum;
use App\Infrastructure\Persistence\Models\BookChapterModel;
use App\Infrastructure\Persistence\Models\BookPageModel;
use Illuminate\Database\Eloquent\Factories\Factory;

final class BookPageModelFactory extends Factory
{
    protected $model = BookPageModel::class;

    public function definition(): array
    {
        return [
            'chapter_id'     => BookChapterModel::factory(),
            'number'         => 1,
            'global_number'  => 1,
            'content'        => $this->faker->paragraphs(3, asText: true),
            'content_format' => ContentFormatEnum::TEXT->value,
            'word_count'     => $this->faker->numberBetween(100, 500),
        ];
    }
}
