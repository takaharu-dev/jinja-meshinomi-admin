<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Database\Factories\AreaGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * エリアのまとまり（都心、下町 など）。
 */
#[Fillable(['name', 'sort_order'])]
class AreaGroup extends Model
{
    /** @use HasFactory<AreaGroupFactory> */
    use HasFactory, HasSortOrder;

    /**
     * @return HasMany<Area, $this>
     */
    public function areas(): HasMany
    {
        return $this->hasMany(Area::class)->ordered();
    }
}
