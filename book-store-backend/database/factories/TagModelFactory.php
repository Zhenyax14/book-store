<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Models\TagModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class TagModelFactory extends Factory
{
    protected $model = TagModel::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
