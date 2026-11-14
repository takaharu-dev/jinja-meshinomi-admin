<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'genre' => '居酒屋',
            'budget' => '3,000円〜',
            'has_sake' => false,
            'solo_friendly' => false,
        ];
    }
}
