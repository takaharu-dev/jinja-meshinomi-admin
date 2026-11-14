<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Photo;
use App\Models\Shrine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'photoable_type' => 'shrine',
            'photoable_id' => Shrine::factory(),
            'path' => 'photos/'.fake()->uuid().'.jpg',
            'caption' => null,
            'sort_order' => 0,
        ];
    }
}
