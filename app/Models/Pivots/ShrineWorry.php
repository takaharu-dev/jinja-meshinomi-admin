<?php

declare(strict_types=1);

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 神社と悩みの紐づけ。
 */
class ShrineWorry extends Pivot
{
    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
