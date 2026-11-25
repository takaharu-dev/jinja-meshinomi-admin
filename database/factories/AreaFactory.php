<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Area;
use App\Models\AreaGroup;
use App\Models\Prefecture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Area>
 */
class AreaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'prefecture_id' => Prefecture::factory(),
            'slug' => fake()->unique()->slug(2),
            'label' => fake()->unique()->city(),
            'area_group_id' => AreaGroup::factory(),
            'sort_order' => 0,
        ];
    }
}
