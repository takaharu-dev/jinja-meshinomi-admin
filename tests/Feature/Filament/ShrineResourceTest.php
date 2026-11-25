<?php

declare(strict_types=1);

use App\Filament\Resources\Shrines\Pages\CreateShrine;
use App\Filament\Resources\Shrines\Pages\EditShrine;
use App\Filament\Resources\Shrines\Pages\ListShrines;
use App\Filament\Resources\Shrines\RelationManagers\ShopsRelationManager;
use App\Filament\Resources\Shrines\RelationManagers\StationsRelationManager;
use App\Filament\Resources\Shrines\RelationManagers\WorriesRelationManager;
use App\Models\Area;
use App\Models\Prefecture;
use App\Models\Shop;
use App\Models\Shrine;
use App\Models\Station;
use App\Models\User;
use App\Models\VisitLog;
use App\Models\Worry;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

/**
 * @return array<string, mixed>
 */
function validShrineInput(array $overrides = []): array
{
    return [
        'name' => '愛宕神社',
        'kana' => 'あたごじんじゃ',
        'slug' => 'atago',
        'area_id' => Area::factory()->create()->id,
        'address' => '東京都港区愛宕1-5-3',
        'catch' => '86段の「出世の石段」を登りきる',
        'benefit' => '出世・仕事運',
        'highlight' => '',
        'after_visit' => '境内の茶屋で抹茶わらび餅',
        'popularity' => 10,
        'has_goshuin' => true,
        'is_published' => false,
        ...$overrides,
    ];
}

describe('一覧', function () {
    it('神社が表示され、名前と読みがなで検索できる', function () {
        $atago = Shrine::factory()->create(['name' => '愛宕神社', 'kana' => 'あたごじんじゃ']);
        $hie = Shrine::factory()->create(['name' => '日枝神社', 'kana' => 'ひえじんじゃ']);

        Livewire::test(ListShrines::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$atago, $hie])
            ->searchTable('あたご')
            ->assertCanSeeTableRecords([$atago])
            ->assertCanNotSeeTableRecords([$hie]);
    });

    it('悩み・エリア・公開状態で絞り込める', function () {
        $career = Worry::factory()->create();
        $minato = Area::factory()->create();
        $published = Shrine::factory()->published()->create(['area_id' => $minato->id]);
        $published->worries()->attach($career, ['is_primary' => true]);
        $draft = Shrine::factory()->create();

        Livewire::test(ListShrines::class)
            ->filterTable('worries', $career->id)
            ->assertCanSeeTableRecords([$published])
            ->assertCanNotSeeTableRecords([$draft])
            ->resetTableFilters()
            ->filterTable('area_id', $minato->id)
            ->assertCanSeeTableRecords([$published])
            ->assertCanNotSeeTableRecords([$draft])
            ->resetTableFilters()
            ->filterTable('is_published', false)
            ->assertCanSeeTableRecords([$draft])
            ->assertCanNotSeeTableRecords([$published]);
    });

    it('一覧から公開・非公開を切り替えられる', function () {
        $shrine = Shrine::factory()->create();

        Livewire::test(ListShrines::class)
            ->call('updateTableColumnState', 'is_published', (string) $shrine->getKey(), true);

        expect($shrine->fresh()->is_published)->toBeTrue();
    });

    it('参拝記録がある神社は消せず、通知で理由を出す', function () {
        $log = VisitLog::factory()->create();

        Livewire::test(ListShrines::class)
            ->callTableAction(DeleteAction::class, $log->shrine)
            ->assertNotified('ほかのデータで使われているため、削除できません');

        expect(Shrine::find($log->shrine_id))->not->toBeNull();
    });
});

describe('作成・編集', function () {
    it('神社を作れる。見どころが空なら null になる', function () {
        Livewire::test(CreateShrine::class)
            ->fillForm(validShrineInput())
            ->call('create')
            ->assertHasNoFormErrors();

        $shrine = Shrine::firstWhere('slug', 'atago');
        expect($shrine->name)->toBe('愛宕神社')
            ->and($shrine->has_goshuin)->toBeTrue()
            ->and($shrine->highlight)->toBeNull();
    });

    it('地酒の情報も一緒に保存できる', function () {
        Livewire::test(CreateShrine::class)
            ->fillForm(validShrineInput(['sake' => ['has_omiki' => true, 'recommended_brand' => '澤乃井']]))
            ->call('create')
            ->assertHasNoFormErrors();

        $sake = Shrine::firstWhere('slug', 'atago')->sake;
        expect($sake->has_omiki)->toBeTrue()
            ->and($sake->recommended_brand)->toBe('澤乃井');
    });

    it('入力内容を検証する', function (array $input, array $errors) {
        $chiyoda = Area::factory()->create(['slug' => 'chiyoda']);
        Shrine::factory()->create(['slug' => 'hie', 'area_id' => $chiyoda->id]);
        // データセットではエリアをスラッグで書く（作る前は id が分からないため）
        if (($input['area_id'] ?? null) === 'chiyoda') {
            $input['area_id'] = $chiyoda->id;
        }

        Livewire::test(CreateShrine::class)
            ->fillForm(validShrineInput($input))
            ->call('create')
            ->assertHasFormErrors($errors);
    })->with([
        '同じエリアでスラッグが重複' => [['slug' => 'hie', 'area_id' => 'chiyoda'], ['slug' => 'unique']],
        'スラッグに大文字' => [['slug' => 'Atago'], ['slug' => 'regex']],
        '名前が空' => [['name' => ''], ['name' => 'required']],
        'エリアが空' => [['area_id' => null], ['area_id' => 'required']],
        '人気度がマイナス' => [['popularity' => -1], ['popularity' => 'min']],
    ]);

    it('エリアが違えば同じスラッグで作れる', function () {
        Shrine::factory()->create(['slug' => 'hikawa-jinja']);

        Livewire::test(CreateShrine::class)
            ->fillForm(validShrineInput(['slug' => 'hikawa-jinja']))
            ->call('create')
            ->assertHasNoFormErrors();

        expect(Shrine::where('slug', 'hikawa-jinja')->count())->toBe(2);
    });

    it('編集画面に公開したときの URL が出る', function () {
        $tokyo = Prefecture::factory()->create(['slug' => 'tokyo']);
        $shrine = Shrine::factory()->create([
            'slug' => 'atago-jinja',
            'area_id' => Area::factory()->create(['slug' => 'minato', 'prefecture_id' => $tokyo->id])->id,
        ]);

        Livewire::test(EditShrine::class, ['record' => $shrine->getRouteKey()])
            ->assertSee('/tokyo/minato/atago-jinja');
    });

    it('編集画面を開いて保存できる', function () {
        $shrine = Shrine::factory()->create();

        Livewire::test(EditShrine::class, ['record' => $shrine->getRouteKey()])
            ->assertOk()
            ->fillForm(['catch' => '新しいキャッチコピー'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($shrine->fresh()->catch)->toBe('新しいキャッチコピー');
    });
});

describe('悩みの紐づけ', function () {
    it('最初に追加した悩みは自動でメインになる', function () {
        $shrine = Shrine::factory()->create();
        $worry = Worry::factory()->create();

        Livewire::test(WorriesRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
            ->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $worry->id])
            ->assertHasNoFormErrors();

        expect($shrine->fresh()->primaryWorry()?->is($worry))->toBeTrue();
    });

    it('2つ目以降は、指定したときだけメインが切り替わる', function () {
        $shrine = Shrine::factory()->create();
        $first = Worry::factory()->create();
        $second = Worry::factory()->create();
        $third = Worry::factory()->create();
        $shrine->setPrimaryWorry($first);

        $component = Livewire::test(WorriesRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class]);
        $component->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $second->id]);
        expect($shrine->fresh()->primaryWorry()?->is($first))->toBeTrue();

        $component->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $third->id, 'make_primary' => true]);
        expect($shrine->fresh()->primaryWorry()?->is($third))->toBeTrue()
            ->and($shrine->fresh()->worries)->toHaveCount(3);
    });

    it('一覧の「メインにする」で切り替えられる', function () {
        $shrine = Shrine::factory()->create();
        $first = Worry::factory()->create();
        $second = Worry::factory()->create();
        $shrine->setPrimaryWorry($first);
        $shrine->worries()->attach($second);

        Livewire::test(WorriesRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
            ->callAction(TestAction::make('makePrimary')->table($second));

        expect($shrine->fresh()->primaryWorry()?->is($second))->toBeTrue();
    });

    it('悩みを外せる', function () {
        $shrine = Shrine::factory()->create();
        $worry = Worry::factory()->create();
        $shrine->setPrimaryWorry($worry);

        Livewire::test(WorriesRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
            ->callAction(TestAction::make(DetachAction::class)->table($worry));

        expect($shrine->fresh()->worries)->toBeEmpty();
    });
});

describe('最寄り駅の紐づけ', function () {
    it('徒歩分数つきで駅を追加でき、最初の駅は最寄りになる', function () {
        $shrine = Shrine::factory()->create();
        $station = Station::factory()->create();

        Livewire::test(StationsRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
            ->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $station->id, 'walk_minutes' => 5])
            ->assertHasNoFormErrors();

        $shrine = $shrine->fresh();
        expect($shrine->nearestStation()?->is($station))->toBeTrue()
            ->and($shrine->isNearStation())->toBeTrue();
    });

    it('「最寄りにする」で切り替えられ、最寄りは1つだけ', function () {
        $shrine = Shrine::factory()->create();
        $a = Station::factory()->create();
        $b = Station::factory()->create();
        $shrine->setNearestStation($a, 3);
        $shrine->stations()->attach($b, ['walk_minutes' => 8]);

        Livewire::test(StationsRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
            ->callAction(TestAction::make('makeNearest')->table($b));

        $shrine = $shrine->fresh();
        expect($shrine->nearestStation()?->is($b))->toBeTrue()
            ->and($shrine->stations->filter(fn (Station $s) => $s->pivot->is_nearest))->toHaveCount(1)
            ->and($shrine->isNearStation())->toBeFalse();
    });

    it('徒歩分数を直せる', function () {
        $shrine = Shrine::factory()->create();
        $station = Station::factory()->create();
        $shrine->setNearestStation($station, 10);

        Livewire::test(StationsRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
            ->callAction(TestAction::make(EditAction::class)->table($station), ['walk_minutes' => 4])
            ->assertHasNoFormErrors();

        expect($shrine->fresh()->nearestStation()->pivot->walk_minutes)->toBe(4);
    });
});

describe('参拝後のお店の紐づけ', function () {
    it('登録済みのお店を、距離とおすすめつきで追加できる', function () {
        $shrine = Shrine::factory()->create();
        $shop = Shop::factory()->create();

        Livewire::test(ShopsRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $shop->id,
                'distance' => '徒歩3分',
                'recommend' => '鴨せいろと冷酒',
            ])
            ->assertHasNoFormErrors();

        $attached = $shrine->fresh()->shops->first();
        expect($attached->is($shop))->toBeTrue()
            ->and($attached->pivot->distance)->toBe('徒歩3分')
            ->and($attached->pivot->recommend)->toBe('鴨せいろと冷酒');
    });

    it('新しいお店をその場で作って追加できる', function () {
        $shrine = Shrine::factory()->create();

        Livewire::test(ShopsRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'name' => '山の上の茶屋',
                'genre' => '甘味',
                'budget' => '800円〜',
                'has_sake' => false,
                'solo_friendly' => true,
                'distance' => '境内',
                'recommend' => '抹茶わらび餅',
            ])
            ->assertHasNoFormErrors();

        $shop = Shop::firstWhere('name', '山の上の茶屋');
        expect($shop->solo_friendly)->toBeTrue()
            ->and($shrine->fresh()->shops->first()->pivot->recommend)->toBe('抹茶わらび餅');
    });

    it('距離とおすすめは必須', function () {
        $shrine = Shrine::factory()->create();
        $shop = Shop::factory()->create();

        Livewire::test(ShopsRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
            ->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $shop->id, 'distance' => '', 'recommend' => ''])
            ->assertHasFormErrors(['distance' => 'required', 'recommend' => 'required']);

        expect($shrine->fresh()->shops)->toBeEmpty();
    });
});
