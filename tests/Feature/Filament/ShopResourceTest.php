<?php

declare(strict_types=1);

use App\Filament\Resources\Shops\Pages\CreateShop;
use App\Filament\Resources\Shops\Pages\EditShop;
use App\Filament\Resources\Shops\Pages\ListShops;
use App\Models\Shop;
use App\Models\Shrine;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('お店が一覧に表示され、店名とジャンルで検索できる', function () {
    $soba = Shop::factory()->create(['name' => '神谷町そば', 'genre' => '蕎麦・日本酒']);
    $bar = Shop::factory()->create(['name' => '赤坂酒場', 'genre' => '居酒屋']);

    Livewire::test(ListShops::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$soba, $bar])
        ->searchTable('蕎麦')
        ->assertCanSeeTableRecords([$soba])
        ->assertCanNotSeeTableRecords([$bar]);
});

it('日本酒が飲めるお店で絞り込める', function () {
    $sake = Shop::factory()->create(['has_sake' => true]);
    $cafe = Shop::factory()->create(['has_sake' => false]);

    Livewire::test(ListShops::class)
        ->filterTable('has_sake', true)
        ->assertCanSeeTableRecords([$sake])
        ->assertCanNotSeeTableRecords([$cafe]);
});

it('お店を作れる', function () {
    Livewire::test(CreateShop::class)
        ->fillForm([
            'name' => '神谷町そば',
            'genre' => '蕎麦・日本酒',
            'budget' => '1,500円〜',
            'has_sake' => true,
            'solo_friendly' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $shop = Shop::firstWhere('name', '神谷町そば');
    expect($shop->has_sake)->toBeTrue()
        ->and($shop->solo_friendly)->toBeTrue();
});

it('店名・ジャンル・予算は必須', function () {
    Livewire::test(CreateShop::class)
        ->fillForm(['name' => '', 'genre' => '', 'budget' => ''])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'genre' => 'required', 'budget' => 'required']);
});

it('編集画面を開いて保存できる', function () {
    $shop = Shop::factory()->create();

    Livewire::test(EditShop::class, ['record' => $shop->getRouteKey()])
        ->assertOk()
        ->fillForm(['budget' => '5,000円〜'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($shop->fresh()->budget)->toBe('5,000円〜');
});

it('神社に紐づいているお店は消せず、通知で理由を出す', function () {
    $shop = Shop::factory()->create();
    Shrine::factory()->create()->shops()->attach($shop, ['distance' => '徒歩3分', 'recommend' => '冷酒']);

    Livewire::test(ListShops::class)
        ->callTableAction(DeleteAction::class, $shop)
        ->assertNotified('ほかのデータで使われているため、削除できません');

    expect(Shop::find($shop->id))->not->toBeNull();
});
