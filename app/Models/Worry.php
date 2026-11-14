<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use App\Models\Pivots\ShrineWorry;
use Database\Factories\WorryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 悩み（仕事・出世、縁結び など）。
 *
 * @property ShrineWorry $pivot
 */
#[Fillable(['slug', 'label', 'icon', 'sort_order'])]
class Worry extends Model
{
    /** @use HasFactory<WorryFactory> */
    use HasFactory, HasSortOrder;

    /**
     * @return HasMany<WorryScene, $this>
     */
    public function scenes(): HasMany
    {
        return $this->hasMany(WorryScene::class)->ordered();
    }

    /**
     * @return BelongsToMany<Shrine, $this, ShrineWorry>
     */
    public function shrines(): BelongsToMany
    {
        return $this->belongsToMany(Shrine::class)
            ->using(ShrineWorry::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }
}
