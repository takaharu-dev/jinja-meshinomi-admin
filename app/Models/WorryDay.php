<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Database\Factories\WorryDayFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 神社詳細の「こんな日に行きたい」に出す、悩みごとの文章。
 */
#[Fillable(['worry_id', 'text', 'sort_order'])]
class WorryDay extends Model
{
    /** @use HasFactory<WorryDayFactory> */
    use HasFactory, HasSortOrder;

    /**
     * @return BelongsTo<Worry, $this>
     */
    public function worry(): BelongsTo
    {
        return $this->belongsTo(Worry::class);
    }
}
