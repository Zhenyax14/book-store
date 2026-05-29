<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Infrastructure\Persistence\Models\BookChapterModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

final class BookChapterModelFactory extends Factory
{
    protected $model = BookChapterModel::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'number'               => 1,
            'title'                => $title,
            'slug'                 => Str::slug($title),
            'reading_time_minutes' => $this->faker->numberBetween(5, 60),
            'is_published'         => true,
        ];
    }
}
