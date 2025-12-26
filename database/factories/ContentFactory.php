<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(),
            'content_type_id' => 1,
            'body' => fake()->paragraphs(3, true),
            'published_at' => fake()->optional()->dateTimeThisYear(),
        ];
    }
}
