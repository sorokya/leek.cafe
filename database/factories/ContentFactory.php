<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
final class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $body = fake()->paragraphs(3, true);

        return [
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(),
            'body' => $body,
            'rendered' => resolve(\App\Services\ContentRenderer::class)->render($body),
            'visibility' => fake()->numberBetween(0, 2),
        ];
    }
}
