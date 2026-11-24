<?php

declare(strict_types=1);

use App\Filament\Resources\Features\Pages\CreateFeature;
use App\Filament\Resources\Features\Pages\EditFeature;
use App\Filament\Resources\Features\Pages\ListFeatures;
use App\Filament\Resources\Features\Tables\FeaturesTable;
use App\Models\Area;
use App\Models\Feature;
use App\Models\User;
use App\Models\Worry;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

/**
 * @return array<string, mixed>
 */
function validFeatureInput(array $overrides = []): array
{
    return [
        'title' => '仕事でモヤモヤした日に行きたい神社%d選',
        'is_numbered' => true,
        'slug' => 'career',
        'summary' => '出世・仕事運の神社と、帰りに寄りたいお店',
        'lead' => "仕事でモヤモヤした日は、神社へ。\n\n参拝のあとは一杯。",
        'conditions' => ['worry' => null, 'area' => null, 'k' => [], 'q' => ''],
        'sort_order' => 0,
        'is_published' => false,
        ...$overrides,
    ];
}

it('一覧に並び順で表示され、条件が読める形で出る', function () {
    $second = Feature::factory()->create(['sort_order' => 2, 'conditions' => ['k' => ['solo']]]);
    $first = Feature::factory()->create(['sort_order' => 1, 'conditions' => ['q' => '神楽坂']]);

    Livewire::test(ListFeatures::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$first, $second], inOrder: true)
        ->assertTableColumnStateSet('conditions', 'キーワード「神楽坂」', $first)
        ->assertTableColumnStateSet('conditions', 'こだわり 一人でも入りやすいお店', $second);
});

it('条件を選んで特集を作れ、空の条件は保存されない', function () {
    $worry = Worry::factory()->create(['slug' => 'career']);
    Area::factory()->create(['slug' => 'minato']);

    Livewire::test(CreateFeature::class)
        ->fillForm(validFeatureInput([
            'conditions' => ['worry' => $worry->slug, 'area' => null, 'k' => ['solo', 'sake'], 'q' => '  '],
        ]))
        ->call('create')
        ->assertHasNoFormErrors();

    $feature = Feature::firstWhere('slug', 'career');
    // MySQL の JSON はキーの順番を保たないので、中身だけを比べる
    expect($feature->conditions)->toEqual(['worry' => 'career', 'k' => ['sake', 'solo']])
        ->and($feature->displayTitle(4))->toBe('仕事でモヤモヤした日に行きたい神社4選');
});

it('件数を入れるのにタイトルに %d がなければエラー', function () {
    Livewire::test(CreateFeature::class)
        ->fillForm(validFeatureInput(['title' => '仕事でモヤモヤした日に行きたい神社']))
        ->call('create')
        ->assertHasFormErrors(['title']);

    expect(Feature::count())->toBe(0);
});

it('件数を入れないなら %d はなくてよい', function () {
    Livewire::test(CreateFeature::class)
        ->fillForm(validFeatureInput(['title' => '一人で行ける神社と一杯', 'is_numbered' => false]))
        ->call('create')
        ->assertHasNoFormErrors();
});

it('編集画面に今の条件が出て、直して保存できる', function () {
    Area::factory()->create(['slug' => 'minato']);
    $feature = Feature::factory()->create(['conditions' => ['q' => '神楽坂', 'k' => ['solo']]]);

    $component = Livewire::test(EditFeature::class, ['record' => $feature->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet(['conditions.q' => '神楽坂', 'conditions.k' => ['solo']]);

    $component
        ->fillForm(['conditions' => ['worry' => null, 'area' => 'minato', 'k' => [], 'q' => '']])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($feature->fresh()->conditions)->toBe(['area' => 'minato']);
});

it('条件の正規化は、知らないこだわりを捨てる', function () {
    expect(Feature::normalizeConditions(['k' => ['solo', 'unknown'], 'q' => 'x', 'extra' => 'y']))
        ->toBe(['k' => ['solo'], 'q' => 'x']);
});

it('条件がないときは「すべての神社」と出す', function () {
    expect(FeaturesTable::describeConditions([]))->toBe('条件なし（すべての神社）');
});
