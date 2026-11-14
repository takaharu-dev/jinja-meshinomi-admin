<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Area;
use App\Models\Shrine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shrine>
 */
class ShrineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'name' => fake()->lastName().'神社',
            'kana' => 'じんじゃ',
            'area_id' => Area::factory(),
            'catch' => fake()->realText(30),
            'benefit' => '開運',
            'address' => fake()->address(),
            'highlight' => null,
            'after_visit' => fake()->realText(20),
            'popularity' => fake()->numberBetween(0, 100),
            'has_goshuin' => false,
            'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(['is_published' => true]);
    }
}
