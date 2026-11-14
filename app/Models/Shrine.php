<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Pivots\ShrineShop;
use App\Models\Pivots\ShrineStation;
use App\Models\Pivots\ShrineWorry;
use Database\Factories\ShrineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * 神社。
 */
#[Fillable([
    'slug', 'name', 'kana', 'area_id', 'catch', 'benefit', 'address', 'highlight',
    'after_visit', 'popularity', 'has_goshuin', 'is_published',
])]
class Shrine extends Model
{
    /** @use HasFactory<ShrineFactory> */
    use HasFactory;

    /** 「駅から徒歩5分以内」とみなす、最寄り駅からの徒歩分数 */
    public const int NEAR_STATION_WALK_MINUTES = 5;

    protected function casts(): array
    {
        return [
            'popularity' => 'integer',
            'has_goshuin' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Area, $this>
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * @return BelongsToMany<Worry, $this, ShrineWorry>
     */
    public function worries(): BelongsToMany
    {
        return $this->belongsToMany(Worry::class)
            ->using(ShrineWorry::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Station, $this, ShrineStation>
     */
    public function stations(): BelongsToMany
    {
        return $this->belongsToMany(Station::class)
            ->using(ShrineStation::class)
            ->withPivot('walk_minutes', 'is_nearest', 'sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * @return BelongsToMany<Shop, $this, ShrineShop>
     */
    public function shops(): BelongsToMany
    {
        // Laravel の規約だと shop_shrine になるので、テーブル名を明示する
        return $this->belongsToMany(Shop::class, 'shrine_shop')
            ->using(ShrineShop::class)
            ->withPivot('distance', 'recommend', 'sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * @return HasOne<ShrineSake, $this>
     */
    public function sake(): HasOne
    {
        return $this->hasOne(ShrineSake::class);
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * 最寄り駅から徒歩5分以内の神社に絞る。
     */
    #[Scope]
    protected function nearStation(Builder $query): void
    {
        $query->whereHas('stations', fn (Builder $q) => $q
            ->where('shrine_station.is_nearest', true)
            ->where('shrine_station.walk_minutes', '<=', self::NEAR_STATION_WALK_MINUTES));
    }

    public function primaryWorry(): ?Worry
    {
        return $this->worries->first(fn (Worry $worry) => $worry->pivot->is_primary);
    }

    public function nearestStation(): ?Station
    {
        return $this->stations->first(fn (Station $station) => $station->pivot->is_nearest);
    }

    public function isNearStation(): bool
    {
        $minutes = $this->nearestStation()?->pivot->walk_minutes;

        return $minutes !== null && $minutes <= self::NEAR_STATION_WALK_MINUTES;
    }

    /**
     * メインの悩みを切り替える。紐づいていなければ紐づける。
     * DB の unique（神社ごとに1つまで）に引っかからないよう、先に今のメインを外す。
     */
    public function setPrimaryWorry(Worry $worry): void
    {
        DB::transaction(function () use ($worry) {
            $this->worries()->newPivotQuery()->update(['is_primary' => false]);
            $this->worries()->syncWithoutDetaching([$worry->id => ['is_primary' => true]]);
        });

        $this->unsetRelation('worries');
    }

    /**
     * 最寄り駅を切り替える。紐づいていなければ紐づける。
     */
    public function setNearestStation(Station $station, ?int $walkMinutes = null): void
    {
        DB::transaction(function () use ($station, $walkMinutes) {
            $this->stations()->newPivotQuery()->update(['is_nearest' => false]);

            $attributes = ['is_nearest' => true];
            if ($walkMinutes !== null) {
                $attributes['walk_minutes'] = $walkMinutes;
            }
            $this->stations()->syncWithoutDetaching([$station->id => $attributes]);
        });

        $this->unsetRelation('stations');
    }
}
