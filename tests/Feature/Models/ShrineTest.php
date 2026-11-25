<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\Prefecture;
use App\Models\Shop;
use App\Models\Shrine;
use App\Models\ShrineSake;
use App\Models\Station;
use App\Models\Worry;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

describe('悩み', function () {
    it('複数の悩みを持てて、メインは1つ', function () {
        $shrine = Shrine::factory()->create();
        [$money, $yakuyoke] = Worry::factory()->count(2)->create();

        $shrine->worries()->attach($yakuyoke);
        $shrine->setPrimaryWorry($money);

        expect($shrine->worries)->toHaveCount(2)
            ->and($shrine->primaryWorry()->is($money))->toBeTrue();
    });

    it('メインの悩みを切り替えると、前のメインは外れる', function () {
        $shrine = Shrine::factory()->create();
        [$a, $b] = Worry::factory()->count(2)->create();

        $shrine->setPrimaryWorry($a);
        $shrine->setPrimaryWorry($b);

        expect($shrine->primaryWorry()->is($b))->toBeTrue()
            ->and($shrine->worries->filter(fn ($w) => $w->pivot->is_primary))->toHaveCount(1);
    });

    it('DB でもメインの悩みは神社ごとに1つまで', function () {
        $shrine = Shrine::factory()->create();
        [$a, $b] = Worry::factory()->count(2)->create();

        $shrine->worries()->attach([$a->id => ['is_primary' => true], $b->id => ['is_primary' => true]]);
    })->throws(UniqueConstraintViolationException::class);

    it('神社に紐づいている悩みは消せない', function () {
        $worry = Worry::factory()->create();
        Shrine::factory()->create()->worries()->attach($worry);

        $worry->delete();
    })->throws(QueryException::class);
});

describe('駅', function () {
    it('駅を sort_order 順に取得できて、最寄り駅は1つ', function () {
        $shrine = Shrine::factory()->create();
        [$akasaka, $tameike] = Station::factory()->count(2)->create();

        $shrine->stations()->attach($akasaka, ['sort_order' => 1]);
        $shrine->stations()->attach($tameike, ['sort_order' => 2]);
        $shrine->setNearestStation($tameike, walkMinutes: 3);

        expect($shrine->stations->pluck('id')->all())->toBe([$akasaka->id, $tameike->id])
            ->and($shrine->nearestStation()->is($tameike))->toBeTrue()
            ->and($shrine->nearestStation()->pivot->walk_minutes)->toBe(3)
            ->and($shrine->stations->first()->pivot->walk_minutes)->toBeNull();
    });

    it('DB でも最寄り駅は神社ごとに1つまで', function () {
        $shrine = Shrine::factory()->create();
        [$a, $b] = Station::factory()->count(2)->create();

        $shrine->stations()->attach([$a->id => ['is_nearest' => true], $b->id => ['is_nearest' => true]]);
    })->throws(UniqueConstraintViolationException::class);

    it('最寄り駅から徒歩5分以内かを判定できる', function (?int $minutes, bool $expected) {
        $shrine = Shrine::factory()->create();
        $shrine->setNearestStation(Station::factory()->create(), walkMinutes: $minutes);

        expect($shrine->isNearStation())->toBe($expected)
            ->and(Shrine::nearStation()->whereKey($shrine->id)->exists())->toBe($expected);
    })->with([
        '5分' => [5, true],
        '6分' => [6, false],
        '分からない' => [null, false],
    ]);

    it('最寄り駅以外が近くても、徒歩5分以内にはならない', function () {
        $shrine = Shrine::factory()->create();
        $shrine->stations()->attach(Station::factory()->create(), ['walk_minutes' => 1]);
        $shrine->setNearestStation(Station::factory()->create(), walkMinutes: 7);

        expect($shrine->isNearStation())->toBeFalse()
            ->and(Shrine::nearStation()->exists())->toBeFalse();
    });
});

describe('お店', function () {
    it('お店を複数の神社で使い回せて、神社ごとに距離とおすすめを持てる', function () {
        $shop = Shop::factory()->create(['has_sake' => true]);
        [$a, $b] = Shrine::factory()->count(2)->create();

        $a->shops()->attach($shop, ['distance' => '徒歩3分', 'recommend' => '鴨せいろと日本酒']);
        $b->shops()->attach($shop, ['distance' => '徒歩10分', 'recommend' => '地酒の飲み比べ']);

        expect($shop->shrines)->toHaveCount(2)
            ->and($a->shops->first()->pivot->distance)->toBe('徒歩3分')
            ->and($b->shops->first()->pivot->recommend)->toBe('地酒の飲み比べ')
            ->and($shop->has_sake)->toBeTrue();
    });

    it('神社に紐づいているお店は消せない', function () {
        $shop = Shop::factory()->create();
        Shrine::factory()->create()->shops()->attach($shop, ['distance' => '徒歩1分', 'recommend' => '日本酒']);

        $shop->delete();
    })->throws(QueryException::class);
});

describe('地酒', function () {
    it('神社と1対1で持てる', function () {
        $sake = ShrineSake::factory()->create(['has_omiki' => true, 'recommended_brand' => '銘柄']);

        expect($sake->shrine->sake->is($sake))->toBeTrue();
    });

    it('1つの神社に2件は持てない', function () {
        $shrine = Shrine::factory()->create();
        ShrineSake::factory()->for($shrine)->create();

        ShrineSake::factory()->for($shrine)->create();
    })->throws(UniqueConstraintViolationException::class);
});

it('神社を消すと、悩み・駅・お店の紐づけと地酒も消える', function () {
    $shrine = Shrine::factory()->create();
    $shrine->worries()->attach(Worry::factory()->create());
    $shrine->stations()->attach(Station::factory()->create());
    $shrine->shops()->attach(Shop::factory()->create(), ['distance' => '徒歩1分', 'recommend' => '日本酒']);
    ShrineSake::factory()->for($shrine)->create();

    $shrine->delete();

    foreach (['shrine_worry', 'shrine_station', 'shrine_shop', 'shrine_sakes'] as $table) {
        $this->assertDatabaseEmpty($table);
    }
    expect(Worry::count())->toBe(1)->and(Station::count())->toBe(1)->and(Shop::count())->toBe(1);
});

it('神社やお店のあるエリアは消せない', function () {
    $area = Area::factory()->create();
    Shrine::factory()->for($area)->create();

    $area->delete();
})->throws(QueryException::class);

it('公開中の神社だけに絞れる', function () {
    Shrine::factory()->published()->create(['slug' => 'open']);
    Shrine::factory()->create(['slug' => 'draft']);

    expect(Shrine::published()->pluck('slug')->all())->toBe(['open']);
});

it('公開したときの URL のパスは /{都道府県}/{市区町村}/{神社}', function () {
    $tokyo = Prefecture::factory()->create(['slug' => 'tokyo']);
    $minato = Area::factory()->create(['slug' => 'minato', 'prefecture_id' => $tokyo->id]);
    $shrine = Shrine::factory()->create(['slug' => 'atago-jinja', 'area_id' => $minato->id]);

    expect($shrine->publicPath())->toBe('/tokyo/minato/atago-jinja');
});

it('同じエリアの中では slug は重複できず、エリアが違えば同じ slug を使える', function () {
    $kawagoe = Area::factory()->create();
    $omiya = Area::factory()->create();
    Shrine::factory()->create(['slug' => 'hikawa-jinja', 'area_id' => $kawagoe->id]);
    Shrine::factory()->create(['slug' => 'hikawa-jinja', 'area_id' => $omiya->id]);

    expect(Shrine::where('slug', 'hikawa-jinja')->count())->toBe(2);

    Shrine::factory()->create(['slug' => 'hikawa-jinja', 'area_id' => $kawagoe->id]);
})->throws(UniqueConstraintViolationException::class);
