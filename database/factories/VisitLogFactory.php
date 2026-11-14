<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Shrine;
use App\Models\VisitLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitLog>
 */
class VisitLogFactory extends Factory
{
    public function definition(): array
    {
        $visitedOn = fake()->date();

        return [
            'slug' => $visitedOn.'-'.fake()->unique()->slug(1),
            'visited_on' => $visitedOn,
            'shrine_id' => Shrine::factory(),
            'shop_id' => null,
            'title' => fake()->realText(20),
            'body' => fake()->realText(100),
            'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(['is_published' => true]);
    }
}
