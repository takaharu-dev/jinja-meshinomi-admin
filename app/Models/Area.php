<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Database\Factories\AreaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * エリア（港区、武蔵野市、鎌倉市 など。市区町村の単位）。URL の2段目（/tokyo/minato/…）になる。
 */
#[Fillable(['prefecture_id', 'slug', 'label', 'area_group_id', 'sort_order'])]
class Area extends Model
{
    /** @use HasFactory<AreaFactory> */
    use HasFactory, HasSortOrder;

    /**
     * @return BelongsTo<Prefecture, $this>
     */
    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }

    /**
     * @return BelongsTo<AreaGroup, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(AreaGroup::class, 'area_group_id');
    }

    /**
     * @return HasMany<Station, $this>
     */
    public function stations(): HasMany
    {
        return $this->hasMany(Station::class);
    }

    /**
     * @return HasMany<Shrine, $this>
     */
    public function shrines(): HasMany
    {
        return $this->hasMany(Shrine::class);
    }
}
