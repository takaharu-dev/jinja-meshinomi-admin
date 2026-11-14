<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Area;
use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Station>
 */
class StationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city().'駅',
            'area_id' => Area::factory(),
        ];
    }
}
