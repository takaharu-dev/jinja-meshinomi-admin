<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Shrine;
use App\Models\ShrineSake;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShrineSake>
 */
class ShrineSakeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shrine_id' => Shrine::factory(),
            'has_omiki' => false,
            'recommended_brand' => null,
        ];
    }
}
