<?php

declare(strict_types=1);

use App\Filament\Resources\AreaGroups\Pages\CreateAreaGroup;
use App\Filament\Resources\AreaGroups\Pages\ListAreaGroups;
use App\Filament\Resources\Areas\Pages\CreateArea;
use App\Filament\Resources\Areas\Pages\ListAreas;
use App\Filament\Resources\Stations\Pages\CreateStation;
use App\Filament\Resources\Stations\Pages\ListStations;
use App\Filament\Resources\Worries\Pages\CreateWorry;
use App\Filament\Resources\Worries\Pages\EditWorry;
use App\Filament\Resources\Worries\Pages\ListWorries;
use App\Models\Area;
use App\Models\AreaGroup;
use App\Models\Prefecture;
use App\Models\Shrine;
use App\Models\Station;
use App\Models\User;
use App\Models\Worry;
use App\Models\WorryScene;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

describe('悩み', function () {
    it('一覧に並び順で表示される', function () {
        $worries = collect([
            Worry::factory()->create(['sort_order' => 2]),
            Worry::factory()->create(['sort_order' => 1]),
        ]);

        Livewire::test(ListWorries::class)
            ->assertOk()
            ->assertCanSeeTableRecords($worries->sortBy('sort_order'), inOrder: true);
    });

    it('こんな日に行きたい と一緒に作れる', function () {
        Livewire::test(CreateWorry::class)
            ->fillForm([
                'label' => '仕事・出世',
                'slug' => 'career',
                'icon' => '出',
                'sort_order' => 1,
                'scenes' => [
                    ['text' => '上司や評価のことで、気持ちが沈んでいる日'],
                    ['text' => '異動や転職など、仕事の節目を迎える前'],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $worry = Worry::firstWhere('slug', 'career');
        expect($worry->icon)->toBe('出')
            ->and($worry->scenes->pluck('text')->all())->toBe([
                '上司や評価のことで、気持ちが沈んでいる日',
                '異動や転職など、仕事の節目を迎える前',
            ]);
    });

    it('編集画面に こんな日に行きたい が出る', function () {
        $scene = WorryScene::factory()->create(['text' => '断ち切りたい習慣があるとき']);

        $component = Livewire::test(EditWorry::class, ['record' => $scene->worry->getRouteKey()])
            ->assertOk();

        expect(collect($component->get('data.scenes'))->pluck('text')->all())->toBe(['断ち切りたい習慣があるとき']);
    });

    it('入力内容を検証する', function (array $input, array $errors) {
        Worry::factory()->create(['slug' => 'career']);

        Livewire::test(CreateWorry::class)
            ->fillForm([...['label' => '金運', 'slug' => 'money', 'icon' => '金', 'sort_order' => 0], ...$input])
            ->call('create')
            ->assertHasFormErrors($errors);
    })->with([
        'スラッグが重複' => [['slug' => 'career'], ['slug' => 'unique']],
        'スラッグに大文字' => [['slug' => 'Money'], ['slug' => 'regex']],
        'スラッグに空白' => [['slug' => 'big money'], ['slug' => 'regex']],
        'アイコンが2文字' => [['icon' => '金運'], ['icon' => 'max']],
        '表示名が空' => [['label' => ''], ['label' => 'required']],
    ]);

    it('エラーメッセージは日本語', function () {
        $component = Livewire::test(CreateWorry::class)
            ->fillForm(['label' => '', 'slug' => 'money', 'icon' => '金', 'sort_order' => 0])
            ->call('create');

        expect($component->errors()->first('data.label'))->toBe('表示名は必須項目です。');
    });

    it('神社に使われている悩みは消せず、通知で理由を出す', function () {
        $worry = Worry::factory()->create();
        Shrine::factory()->create()->worries()->attach($worry);

        Livewire::test(ListWorries::class)
            ->callTableAction(DeleteAction::class, $worry)
            ->assertNotified('ほかのデータで使われているため、削除できません');

        expect(Worry::find($worry->id))->not->toBeNull();
    });

    it('使われていない悩みは消せる', function () {
        $worry = Worry::factory()->create();

        Livewire::test(ListWorries::class)
            ->callTableAction(DeleteAction::class, $worry);

        expect(Worry::find($worry->id))->toBeNull();
    });
});

describe('エリアグループ・エリア', function () {
    it('エリアグループを作れる', function () {
        Livewire::test(CreateAreaGroup::class)
            ->fillForm(['name' => '都心', 'sort_order' => 1])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(AreaGroup::where('name', '都心')->exists())->toBeTrue();
    });

    it('エリアが残っているエリアグループは消せない', function () {
        $area = Area::factory()->create();

        Livewire::test(ListAreaGroups::class)
            ->callTableAction(DeleteAction::class, $area->group)
            ->assertNotified('ほかのデータで使われているため、削除できません');
    });

    it('エリアを都道府県・エリアグループ付きで作れる', function () {
        $group = AreaGroup::factory()->create();
        $tokyo = Prefecture::factory()->create();

        Livewire::test(CreateArea::class)
            ->fillForm(['prefecture_id' => $tokyo->id, 'label' => '港区', 'slug' => 'minato', 'area_group_id' => $group->id, 'sort_order' => 3])
            ->call('create')
            ->assertHasNoFormErrors();

        $area = Area::firstWhere('slug', 'minato');
        expect($area->group->is($group))->toBeTrue()
            ->and($area->prefecture->is($tokyo))->toBeTrue();
    });

    it('エリアのスラッグは、同じ都道府県の中でだけ重複を調べる', function () {
        $tokyo = Prefecture::factory()->create();
        $kanagawa = Prefecture::factory()->create();
        Area::factory()->create(['slug' => 'midori', 'prefecture_id' => $tokyo->id]);
        $group = AreaGroup::factory()->create();

        Livewire::test(CreateArea::class)
            ->fillForm(['prefecture_id' => $tokyo->id, 'label' => '緑区', 'slug' => 'midori', 'area_group_id' => $group->id])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);

        Livewire::test(CreateArea::class)
            ->fillForm(['prefecture_id' => $kanagawa->id, 'label' => '緑区', 'slug' => 'midori', 'area_group_id' => $group->id])
            ->call('create')
            ->assertHasNoFormErrors();
    });

    it('エリアをエリアグループで絞り込める', function () {
        $toshin = Area::factory()->create();
        $shitamachi = Area::factory()->create();

        Livewire::test(ListAreas::class)
            ->filterTable('area_group_id', $toshin->area_group_id)
            ->assertCanSeeTableRecords([$toshin])
            ->assertCanNotSeeTableRecords([$shitamachi]);
    });
});

describe('駅', function () {
    it('駅を作れて、同じ駅名は登録できない', function () {
        $area = Area::factory()->create();

        Livewire::test(CreateStation::class)
            ->fillForm(['name' => '神谷町駅', 'area_id' => $area->id])
            ->call('create')
            ->assertHasNoFormErrors();

        Livewire::test(CreateStation::class)
            ->fillForm(['name' => '神谷町駅', 'area_id' => $area->id])
            ->call('create')
            ->assertHasFormErrors(['name' => 'unique']);
    });

    it('駅名で検索できる', function () {
        $kamiyacho = Station::factory()->create(['name' => '神谷町駅']);
        $roppongi = Station::factory()->create(['name' => '六本木駅']);

        Livewire::test(ListStations::class)
            ->searchTable('神谷町')
            ->assertCanSeeTableRecords([$kamiyacho])
            ->assertCanNotSeeTableRecords([$roppongi]);
    });
});

it('管理画面のメニューにマスタが「設定」として日本語で並ぶ', function () {
    $this->get('/admin')
        ->assertOk()
        ->assertSeeInOrder(['設定（ふだんは触らない）', '悩み', 'エリアグループ', 'エリア', '駅']);
});
