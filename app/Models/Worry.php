<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Database\Factories\WorryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 悩み（仕事・出世、縁結び など）。
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
}
