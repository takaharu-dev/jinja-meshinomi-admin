<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Database\Factories\FeatureFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 特集。conditions（本体の /api/shrines と同じ検索条件）に当てはまる神社を自動で並べる。
 *
 * conditions の形：{"worry": "career", "area": "minato", "k": ["solo"], "q": "神楽坂"}（どれも省略可）
 */
#[Fillable(['slug', 'title', 'is_numbered', 'summary', 'lead', 'conditions', 'sort_order', 'is_published'])]
class Feature extends Model
{
    /** @use HasFactory<FeatureFactory> */
    use HasFactory, HasSortOrder;

    protected function casts(): array
    {
        return [
            'is_numbered' => 'boolean',
            'conditions' => 'array',
            'is_published' => 'boolean',
        ];
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * 表示用のタイトル。is_numbered のときは %d に件数を入れる（例：仕事でモヤモヤした日に行きたい神社5選）。
     */
    public function displayTitle(int $count): string
    {
        return $this->is_numbered ? sprintf($this->title, $count) : $this->title;
    }
}
