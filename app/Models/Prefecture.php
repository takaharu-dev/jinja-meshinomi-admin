<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Database\Factories\PrefectureFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 都道府県（東京都、神奈川県 など）。URL の1段目（/tokyo/…）になる。
 */
#[Fillable(['slug', 'label', 'sort_order'])]
class Prefecture extends Model
{
    /** @use HasFactory<PrefectureFactory> */
    use HasFactory, HasSortOrder;

    /**
     * @return HasMany<Area, $this>
     */
    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }
}
