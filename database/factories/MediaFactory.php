<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['Anime', 'Manga']);

        return [
            'title' => ucwords(fake()->unique()->words(3, true)),
            'type' => $type,
            'genre' => fake()->randomElement(['Action, Fantasy', 'Adventure, Drama', 'Comedy, Slice of Life', 'Mystery, Supernatural']),
            'creator' => fake()->name(),
            'synopsis' => fake()->paragraph(2),
            'total_units' => $type === 'Anime' ? fake()->numberBetween(12, 64) : fake()->numberBetween(5, 35),
            'status' => fake()->randomElement(['Ongoing', 'Completed']),
            'release_year' => fake()->numberBetween(2015, 2026),
        ];
    }
}
