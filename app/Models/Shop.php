<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Pivots\ShrineShop;
use Database\Factories\ShopFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 参拝後に寄るお店。神社・参拝記録から使い回す。
 *
 * @property ShrineShop $pivot
 */
#[Fillable(['name', 'genre', 'budget', 'has_sake', 'solo_friendly'])]
class Shop extends Model
{
    /** @use HasFactory<ShopFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'has_sake' => 'boolean',
            'solo_friendly' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Shrine, $this, ShrineShop>
     */
    public function shrines(): BelongsToMany
    {
        return $this->belongsToMany(Shrine::class, 'shrine_shop')
            ->using(ShrineShop::class)
            ->withPivot('distance', 'recommend', 'sort_order')
            ->withTimestamps();
    }
}
