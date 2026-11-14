<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Pivots\ShrineStation;
use Database\Factories\StationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property ShrineStation $pivot
 */
#[Fillable(['name', 'area_id'])]
class Station extends Model
{
    /** @use HasFactory<StationFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Area, $this>
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * @return BelongsToMany<Shrine, $this, ShrineStation>
     */
    public function shrines(): BelongsToMany
    {
        return $this->belongsToMany(Shrine::class)
            ->using(ShrineStation::class)
            ->withPivot('walk_minutes', 'is_nearest', 'sort_order')
            ->withTimestamps();
    }
}
