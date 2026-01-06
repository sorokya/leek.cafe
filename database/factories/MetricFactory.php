<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Metric;
use App\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Metric>
 */
final class MetricFactory extends Factory
{
    protected $model = Metric::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->unique()->words(2, true),
            'visibility' => Visibility::PRIVATE,
            'icon' => null,
            'color' => null,
            'min' => null,
            'max' => null,
            'options' => null,
        ];
    }
}
