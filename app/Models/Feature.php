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

    /**
     * conditions の k（こだわり）に入れられる値。本体の backend/data.go の kodawaris と揃える。
     *
     * @var array<string, string>
     */
    public const array KODAWARI = [
        'goshuin' => '御朱印あり',
        'near-station' => '駅から徒歩5分以内',
        'sake' => '日本酒が飲めるお店あり',
        'solo' => '一人でも入りやすいお店',
    ];

    /** conditions に入れられるキー */
    public const array CONDITION_KEYS = ['worry', 'area', 'k', 'q'];

    /**
     * 空の条件を取り除き、本体の検索条件の形（{"worry": "career", "k": ["solo"]} など）にそろえる。
     *
     * @param  array<string, mixed>  $conditions
     * @return array<string, string|list<string>>
     */
    public static function normalizeConditions(array $conditions): array
    {
        $normalized = [];

        foreach (self::CONDITION_KEYS as $key) {
            $value = $conditions[$key] ?? null;

            if ($key === 'k') {
                $value = array_values(array_intersect(array_keys(self::KODAWARI), (array) $value));
                if ($value !== []) {
                    $normalized[$key] = $value;
                }

                continue;
            }

            $value = is_string($value) ? trim($value) : null;
            if ($value !== null && $value !== '') {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

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
