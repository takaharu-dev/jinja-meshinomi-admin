<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Worry;
use App\Models\WorryScene;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorryScene>
 */
class WorrySceneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'worry_id' => Worry::factory(),
            'text' => fake()->realText(30),
            'sort_order' => 0,
        ];
    }
}
