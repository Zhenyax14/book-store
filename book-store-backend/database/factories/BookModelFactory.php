<?php

namespace Database\Factories;

use App\Domain\Shared\ValueObjects\Currency;
use App\Infrastructure\Persistence\Models\BookModel;
use Illuminate\Database\Eloquent\Factories\Factory;

final class BookModelFactory extends Factory
{
    protected $model = BookModel::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'language' => 'en',
            'access_type' => $this->faker->randomElement(['free', 'subscription', 'purchase']),
            'price' => $this->faker->numberBetween(0, 100000),
            'currency' => $this->faker->randomElement(Currency::SUPPORTED),
            'status' => 'draft',
            'published_at' => null,
            'file_path' => null,
        ];
    }

    public function published(): self
    {
        return $this->state([
            'status'       => 'published',
            'published_at' => now()->subDay(),
        ]);
    }

    public function free(): self
    {
        return $this->state([
            'access_type' => 'free',
            'price'       => 0,
        ]);
    }
}
