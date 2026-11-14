<?php

declare(strict_types=1);

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 神社と最寄り駅の紐づけ。
 */
class ShrineStation extends Pivot
{
    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'walk_minutes' => 'integer',
            'is_nearest' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
