<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Worry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Worry>
 */
class WorryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'label' => fake()->realText(10),
            'icon' => mb_substr(fake()->realText(10), 0, 1),
            'sort_order' => 0,
        ];
    }
}
