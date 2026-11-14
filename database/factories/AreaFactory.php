<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Area;
use App\Models\AreaGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Area>
 */
class AreaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'label' => fake()->unique()->city(),
            'area_group_id' => AreaGroup::factory(),
            'sort_order' => 0,
        ];
    }
}
