<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasPhotos;
use Database\Factories\VisitLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 参拝記録。
 */
#[Fillable(['slug', 'visited_on', 'shrine_id', 'shop_id', 'title', 'body', 'is_published'])]
class VisitLog extends Model
{
    /** @use HasFactory<VisitLogFactory> */
    use HasFactory, HasPhotos;

    protected function casts(): array
    {
        return [
            'visited_on' => 'date',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Shrine, $this>
     */
    public function shrine(): BelongsTo
    {
        return $this->belongsTo(Shrine::class);
    }

    /**
     * @return BelongsTo<Shop, $this>
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    #[Scope]
    protected function latestVisited(Builder $query): void
    {
        $query->orderByDesc('visited_on')->orderByDesc('id');
    }

    /**
     * 本文を段落（空行区切り）に分ける。本体の API は段落の配列で返している。
     *
     * @return list<string>
     */
    public function paragraphs(): array
    {
        return array_values(array_filter(
            array_map(trim(...), preg_split('/\R\s*\R/u', $this->body) ?: []),
            fn (string $p): bool => $p !== '',
        ));
    }
}
