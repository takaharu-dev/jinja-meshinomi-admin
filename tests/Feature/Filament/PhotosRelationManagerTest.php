<?php

declare(strict_types=1);

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Shops\Pages\EditShop;
use App\Filament\Resources\Shrines\Pages\EditShrine;
use App\Models\Photo;
use App\Models\Shop;
use App\Models\Shrine;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
});

it('写真をアップロードでき、設定のディスクとフォルダに保存される', function (string $pageClass, Closure $make) {
    $owner = $make();

    Livewire::test(PhotosRelationManager::class, ['ownerRecord' => $owner, 'pageClass' => $pageClass])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'path' => UploadedFile::fake()->image('torii.jpg', 1200, 800),
            'caption' => '鳥居',
        ])
        ->assertHasNoFormErrors();

    $photo = $owner->photos()->sole();
    expect($photo->caption)->toBe('鳥居')
        ->and($photo->path)->toStartWith(config('admin.photos.directory').'/');
    Storage::disk('public')->assertExists($photo->path);
})->with([
    '神社' => [EditShrine::class, fn () => Shrine::factory()->create()],
    'お店' => [EditShop::class, fn () => Shop::factory()->create()],
]);

it('追加した写真は最後に並ぶ', function () {
    $shrine = Shrine::factory()->create();
    Photo::factory()->for($shrine, 'photoable')->create(['caption' => '一枚目', 'sort_order' => 5]);

    Livewire::test(PhotosRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'path' => UploadedFile::fake()->image('honden.jpg'),
            'caption' => '二枚目',
        ]);

    expect($shrine->fresh()->photos->pluck('caption')->all())->toBe(['一枚目', '二枚目']);
});

it('画像以外はアップロードできない', function () {
    $shrine = Shrine::factory()->create();

    Livewire::test(PhotosRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'path' => UploadedFile::fake()->create('memo.pdf', 100, 'application/pdf'),
        ])
        ->assertHasFormErrors(['path']);

    expect($shrine->photos()->count())->toBe(0);
});

it('写真を消すとファイルも消える', function () {
    $shrine = Shrine::factory()->create();
    $path = UploadedFile::fake()->image('photo.jpg')->store('photos', 'public');
    $photo = $shrine->photos()->create(['path' => $path]);

    Livewire::test(PhotosRelationManager::class, ['ownerRecord' => $shrine, 'pageClass' => EditShrine::class])
        ->callAction(TestAction::make(DeleteAction::class)->table($photo));

    expect(Photo::find($photo->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
