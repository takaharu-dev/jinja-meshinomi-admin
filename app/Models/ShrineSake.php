<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ShrineSakeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 神社の地酒情報（神社と1対1）。
 */
#[Fillable(['shrine_id', 'has_omiki', 'recommended_brand'])]
class ShrineSake extends Model
{
    /** @use HasFactory<ShrineSakeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'has_omiki' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Shrine, $this>
     */
    public function shrine(): BelongsTo
    {
        return $this->belongsTo(Shrine::class);
    }
}
