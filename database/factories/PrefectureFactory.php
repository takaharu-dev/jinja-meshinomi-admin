<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Prefecture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prefecture>
 */
class PrefectureFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(1),
            'label' => fake()->unique()->prefecture(),
            'sort_order' => 0,
        ];
    }
}
