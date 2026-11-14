<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AreaGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AreaGroup>
 */
class AreaGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'sort_order' => 0,
        ];
    }
}
