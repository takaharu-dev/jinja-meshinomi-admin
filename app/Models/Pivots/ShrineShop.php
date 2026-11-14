<?php

declare(strict_types=1);

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 神社と、参拝後に寄るお店の紐づけ。
 */
class ShrineShop extends Pivot
{
    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
