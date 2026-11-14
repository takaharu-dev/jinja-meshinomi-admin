<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Database\Factories\AreaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * エリア（港区、武蔵野市 など）。
 */
#[Fillable(['slug', 'label', 'area_group_id', 'sort_order'])]
class Area extends Model
{
    /** @use HasFactory<AreaFactory> */
    use HasFactory, HasSortOrder;

    /**
     * @return BelongsTo<AreaGroup, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(AreaGroup::class, 'area_group_id');
    }
}
