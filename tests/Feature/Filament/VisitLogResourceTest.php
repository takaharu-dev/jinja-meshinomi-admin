<?php

declare(strict_types=1);

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\VisitLogs\Pages\CreateVisitLog;
use App\Filament\Resources\VisitLogs\Pages\EditVisitLog;
use App\Filament\Resources\VisitLogs\Pages\ListVisitLogs;
use App\Models\Shop;
use App\Models\Shrine;
use App\Models\User;
use App\Models\VisitLog;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('一覧に新しい順で表示され、神社と公開状態で絞り込める', function () {
    $hie = Shrine::factory()->create();
    $old = VisitLog::factory()->create(['visited_on' => '2026-10-17']);
    $new = VisitLog::factory()->published()->create(['visited_on' => '2026-10-24', 'shrine_id' => $hie->id]);

    Livewire::test(ListVisitLogs::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$new, $old], inOrder: true)
        ->filterTable('shrine_id', $hie->id)
        ->assertCanSeeTableRecords([$new])
        ->assertCanNotSeeTableRecords([$old])
        ->resetTableFilters()
        ->filterTable('is_published', true)
        ->assertCanSeeTableRecords([$new])
        ->assertCanNotSeeTableRecords([$old]);
});

it('参拝記録を作れ、お店は空でもよい', function (?bool $withShop) {
    $shrine = Shrine::factory()->create();
    $shop = $withShop ? Shop::factory()->create() : null;

    Livewire::test(CreateVisitLog::class)
        ->fillForm([
            'title' => '日枝神社 → 赤坂で軽く一杯',
            'visited_on' => '2026-10-24',
            'slug' => '2026-10-24-hie',
            'shrine_id' => $shrine->id,
            'shop_id' => $shop?->id,
            'body' => "山王男坂を登って参拝。\n\n帰りに赤坂で一杯。",
            'is_published' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $log = VisitLog::firstWhere('slug', '2026-10-24-hie');
    expect($log->visited_on->toDateString())->toBe('2026-10-24')
        ->and($log->shop_id)->toBe($shop?->id)
        ->and($log->paragraphs())->toBe(['山王男坂を登って参拝。', '帰りに赤坂で一杯。']);
})->with([
    'お店あり' => [true],
    'お店なし' => [false],
]);

it('入力内容を検証する', function (array $input, array $errors) {
    VisitLog::factory()->create(['slug' => '2026-10-24-hie']);

    Livewire::test(CreateVisitLog::class)
        ->fillForm([
            'title' => 'タイトル',
            'visited_on' => '2026-10-20',
            'slug' => '2026-10-20-akagi',
            'shrine_id' => Shrine::factory()->create()->id,
            'body' => '本文',
            ...$input,
        ])
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'スラッグが重複' => [['slug' => '2026-10-24-hie'], ['slug' => 'unique']],
    '神社が空' => [['shrine_id' => null], ['shrine_id' => 'required']],
    '本文が空' => [['body' => ''], ['body' => 'required']],
    '参拝した日が未来' => [['visited_on' => now()->addDays(2)->toDateString()], ['visited_on']],
]);

it('編集画面を開いて保存できる', function () {
    $log = VisitLog::factory()->create();

    Livewire::test(EditVisitLog::class, ['record' => $log->getRouteKey()])
        ->assertOk()
        ->fillForm(['title' => '新しいタイトル'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($log->fresh()->title)->toBe('新しいタイトル');
});

it('参拝記録に写真を付けられる', function () {
    Storage::fake('public');
    $log = VisitLog::factory()->create();

    Livewire::test(PhotosRelationManager::class, ['ownerRecord' => $log, 'pageClass' => EditVisitLog::class])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'path' => UploadedFile::fake()->image('soba.jpg'),
            'caption' => '帰りの蕎麦',
        ])
        ->assertHasNoFormErrors();

    expect($log->photos()->sole()->caption)->toBe('帰りの蕎麦');
});
