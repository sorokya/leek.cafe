<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Habit;
use App\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Habit>
 */
final class HabitFactory extends Factory
{
    protected $model = Habit::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->unique()->words(2, true),
            'visibility' => Visibility::PRIVATE,
            'icon' => null,
            'color' => null,
        ];
    }
}
