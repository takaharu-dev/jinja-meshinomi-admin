<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feature>
 */
class FeatureFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'title' => fake()->realText(20),
            'is_numbered' => false,
            'summary' => fake()->realText(20),
            'lead' => fake()->realText(100),
            'conditions' => ['q' => fake()->word()],
            'sort_order' => 0,
            'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(['is_published' => true]);
    }
}
