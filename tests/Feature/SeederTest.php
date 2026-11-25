<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\AreaGroup;
use App\Models\Feature;
use App\Models\Prefecture;
use App\Models\Shop;
use App\Models\Shrine;
use App\Models\Station;
use App\Models\User;
use App\Models\VisitLog;
use App\Models\Worry;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ShrineSeeder;

it('本体の直書きデータが入る', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Worry::count())->toBe(8)
        ->and(AreaGroup::count())->toBe(4)
        ->and(Area::count())->toBe(12)
        ->and(Shrine::count())->toBe(8)
        ->and(Feature::count())->toBe(4)
        ->and(Worry::firstWhere('slug', 'career')->scenes)->toHaveCount(3)
        ->and(Prefecture::count())->toBe(1)
        ->and(Area::firstWhere('slug', 'minato')->group->name)->toBe('都心')
        ->and(Area::firstWhere('slug', 'minato')->prefecture->slug)->toBe('tokyo');
});

it('神社の悩み・最寄り駅・徒歩分数が本体と同じになる', function () {
    $this->seed(DatabaseSeeder::class);

    $hie = Shrine::firstWhere('slug', 'hie-jinja');
    expect($hie->primaryWorry()->slug)->toBe('career')
        ->and($hie->nearestStation()->name)->toBe('溜池山王駅')
        ->and($hie->nearestStation()->pivot->walk_minutes)->toBe(3)
        ->and($hie->stations->pluck('name')->all())->toBe(['溜池山王駅', '赤坂駅'])
        ->and($hie->has_goshuin)->toBeTrue();

    expect(Shrine::firstWhere('slug', 'atago-jinja')->highlight)->toBe('86段の急な石段と、山の上の静かな境内')
        ->and(Shrine::firstWhere('slug', 'koami-jinja')->isNearStation())->toBeFalse();
});

it('参拝していない神社と、お店がまだない特集は非公開で入る', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Shrine::where('is_published', false)->pluck('slug')->sort()->values()->all())
        ->toBe(['asagaya-shinmeigu', 'izumo-taisha-tokyo-bunshi'])
        ->and(Feature::where('is_published', false)->pluck('slug')->all())->toBe(['solo']);
});

it('仮置きのお店・参拝記録・ユーザーは入れない', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Shop::count())->toBe(0)
        ->and(VisitLog::count())->toBe(0)
        ->and(User::count())->toBe(0);
});

it('何度流しても同じ結果になる', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(Shrine::count())->toBe(8)
        ->and(Station::count())->toBe(10)
        ->and(Worry::firstWhere('slug', 'love')->scenes)->toHaveCount(3)
        ->and(Shrine::firstWhere('slug', 'atago-jinja')->worries)->toHaveCount(1)
        ->and(Shrine::firstWhere('slug', 'atago-jinja')->stations)->toHaveCount(2);
});

it('特集の条件は本体と同じ形', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Feature::firstWhere('slug', 'career')->conditions)->toBe(['worry' => 'career'])
        ->and(Feature::firstWhere('slug', 'solo')->conditions)->toBe(['k' => ['solo']])
        ->and(Feature::query()->ordered()->pluck('slug')->all())->toBe(['career', 'kagurazaka-night', 'yakuyoke-kanpai', 'solo']);
});

it('神社の URL は /tokyo/{区}/{正式名のローマ字} になり、本体の古い ID すべてに対応がある', function () {
    $this->seed(DatabaseSeeder::class);

    $atago = Shrine::with('area.prefecture')->firstWhere('slug', 'atago-jinja');
    expect($atago->publicPath())->toBe('/tokyo/minato/atago-jinja')
        ->and(array_values(ShrineSeeder::LEGACY_IDS))->toEqualCanonicalizing(Shrine::pluck('slug')->all());
});
