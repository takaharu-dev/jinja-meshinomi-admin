<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\AreaGroup;
use App\Models\Prefecture;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

it('エリアグループに属するエリアを sort_order 順に取得できる', function () {
    $group = AreaGroup::factory()->create(['name' => '都心']);
    Area::factory()->for($group, 'group')->create(['slug' => 'minato', 'sort_order' => 3]);
    Area::factory()->for($group, 'group')->create(['slug' => 'chiyoda', 'sort_order' => 1]);

    expect($group->areas->pluck('slug')->all())->toBe(['chiyoda', 'minato'])
        ->and(Area::firstWhere('slug', 'minato')->group->name)->toBe('都心');
});

it('エリアが残っているエリアグループは消せない', function () {
    $area = Area::factory()->create();

    $area->group->delete();
})->throws(QueryException::class);

it('エリアが無いエリアグループは消せる', function () {
    $group = AreaGroup::factory()->create();

    $group->delete();

    expect(AreaGroup::find($group->id))->toBeNull();
});

it('同じ都道府県の中では slug は重複できない', function () {
    $tokyo = Prefecture::factory()->create();
    Area::factory()->create(['slug' => 'minato', 'prefecture_id' => $tokyo->id]);

    Area::factory()->create(['slug' => 'minato', 'prefecture_id' => $tokyo->id]);
})->throws(UniqueConstraintViolationException::class);

it('都道府県が違えば同じ slug を使える', function () {
    Area::factory()->create(['slug' => 'midori']);
    Area::factory()->create(['slug' => 'midori']);

    expect(Area::where('slug', 'midori')->count())->toBe(2);
});

it('都道府県に属する', function () {
    $tokyo = Prefecture::factory()->create(['slug' => 'tokyo', 'label' => '東京都']);
    $area = Area::factory()->create(['prefecture_id' => $tokyo->id]);

    expect($area->prefecture->is($tokyo))->toBeTrue()
        ->and($tokyo->areas)->toHaveCount(1);
});
