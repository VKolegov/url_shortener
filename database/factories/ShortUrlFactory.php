<?php

namespace Database\Factories;

use App\Helpers\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShortUrl>
 */
class ShortUrlFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->bothify('?#?'),
            'name' => fake()->words(2, true),
            'destination_url' => fake()->url,
        ];
    }
}
