<?php

declare(strict_types=1);

use App\Filament\Resources\Shrines\Pages\CreateShrine;
use App\Filament\Resources\Shrines\Pages\ListShrines;
use App\Filament\Widgets\GuideWidget;
use App\Models\Photo;
use App\Models\Shrine;
use App\Models\User;
use Filament\Actions\CreateAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('メニューが「登録する」「一覧を見る」「設定」の順に並び、登録画面への近道がある', function () {
    $this->get('/admin')
        ->assertOk()
        ->assertSeeInOrder([
            '＋ 登録する', '神社を登録', '参拝記録を書く', 'お店を登録',
            '一覧を見る', '神社', '参拝記録', 'お店', '特集',
            '設定（ふだんは触らない）',
        ]);
});

it('メニューの近道から神社の登録画面が開き、見出しは「神社を登録」', function () {
    $this->get('/admin/shrines/create')
        ->assertOk()
        ->assertSee('神社を登録');

    Livewire::test(CreateShrine::class)->assertSee('神社を登録');
});

it('一覧の見出しは「神社の一覧」、右上のボタンは「神社を登録」', function () {
    Livewire::test(ListShrines::class)
        ->assertSee('神社の一覧')
        ->assertActionHasLabel(CreateAction::class, '神社を登録');
});

it('データがないときの一覧に、次にやることを出す', function () {
    Livewire::test(ListShrines::class)
        ->assertSee('まだ登録されていません')
        ->assertSee('右上のボタンから追加できます。');
});

it('神社の一覧のタブで、公開中・非公開・写真がないを切り替えられる', function () {
    $published = Shrine::factory()->published()->create();
    $draft = Shrine::factory()->create();
    Photo::factory()->for($published, 'photoable')->create();

    Livewire::test(ListShrines::class)
        ->set('activeTab', 'published')
        ->assertCanSeeTableRecords([$published])
        ->assertCanNotSeeTableRecords([$draft])
        ->set('activeTab', 'draft')
        ->assertCanSeeTableRecords([$draft])
        ->assertCanNotSeeTableRecords([$published])
        ->set('activeTab', 'no_photos')
        ->assertCanSeeTableRecords([$draft])
        ->assertCanNotSeeTableRecords([$published]);
});

it('神社を作ると、タブで紐づけと写真を追加することを通知で伝える', function () {
    Livewire::test(CreateShrine::class)
        ->fillForm(validShrineInput())
        ->call('create')
        ->assertNotified();
});

it('ダッシュボードの使い方に、各画面の件数と入り口が出る', function () {
    Shrine::factory()->count(2)->published()->create();
    Shrine::factory()->create();

    Livewire::test(GuideWidget::class)
        ->assertSee('使い方')
        ->assertSee('3件（公開 2）')
        ->assertSee('一覧を見る')
        ->assertSee('登録する')
        ->assertSee(route('filament.admin.resources.shrines.create'), escape: false);
});
