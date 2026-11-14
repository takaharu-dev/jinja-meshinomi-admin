<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\AreaGroup;
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

it('slug は重複できない', function () {
    Area::factory()->create(['slug' => 'minato']);

    Area::factory()->create(['slug' => 'minato']);
})->throws(UniqueConstraintViolationException::class);
