<?php

declare(strict_types=1);

use App\Models\Photo;
use App\Models\Shop;
use App\Models\Shrine;
use App\Models\User;
use App\Models\VisitLog;
use Illuminate\Database\ClassMorphViolationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

function storePhotoFor(Shrine|VisitLog|Shop $model, string $caption = ''): Photo
{
    $path = UploadedFile::fake()->image('photo.jpg')->store(config('admin.photos.directory'), config('admin.photos.disk'));

    return $model->photos()->create(['path' => $path, 'caption' => $caption]);
}

it('神社・参拝記録・お店に写真を付けられ、type には短い名前が入る', function (string $expectedType, Closure $make) {
    $photo = storePhotoFor($make());

    $this->assertDatabaseHas('photos', ['id' => $photo->id, 'photoable_type' => $expectedType]);
    expect($photo->photoable)->not->toBeNull();
})->with([
    '神社' => ['shrine', fn () => Shrine::factory()->create()],
    '参拝記録' => ['visit_log', fn () => VisitLog::factory()->create()],
    'お店' => ['shop', fn () => Shop::factory()->create()],
]);

it('写真を sort_order 順に取得できる', function () {
    $shrine = Shrine::factory()->create();
    Photo::factory()->for($shrine, 'photoable')->create(['caption' => '御朱印', 'sort_order' => 2]);
    Photo::factory()->for($shrine, 'photoable')->create(['caption' => '境内', 'sort_order' => 1]);

    expect($shrine->photos->pluck('caption')->all())->toBe(['境内', '御朱印']);
});

it('写真を消すとファイルも消える', function () {
    $photo = storePhotoFor(Shrine::factory()->create());
    Storage::disk('public')->assertExists($photo->path);

    $photo->delete();

    Storage::disk('public')->assertMissing($photo->path);
});

it('親を消すと写真とファイルも消える', function () {
    $shrine = Shrine::factory()->create();
    $photo = storePhotoFor($shrine);

    $shrine->delete();

    $this->assertDatabaseEmpty('photos');
    Storage::disk('public')->assertMissing($photo->path);
});

it('親の削除が restrict で失敗したときは、写真は消えない', function () {
    $log = VisitLog::factory()->create();
    $photo = storePhotoFor($log->shrine);

    expect(fn () => $log->shrine->delete())->toThrow(QueryException::class);

    $this->assertDatabaseHas('photos', ['id' => $photo->id]);
    Storage::disk('public')->assertExists($photo->path);
});

it('ディスクは設定で切り替えられる', function () {
    Storage::fake('s3');
    config(['admin.photos.disk' => 's3']);

    $photo = storePhotoFor(Shrine::factory()->create());

    Storage::disk('s3')->assertExists($photo->path);
    expect(Photo::disk())->toBe(Storage::disk('s3'));
});

it('morph map に無いモデルには写真を付けられない', function () {
    Photo::factory()->for(User::factory(), 'photoable')->create();
})->throws(ClassMorphViolationException::class);
