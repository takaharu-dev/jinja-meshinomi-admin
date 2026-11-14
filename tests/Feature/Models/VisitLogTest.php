<?php

declare(strict_types=1);

use App\Models\Shop;
use App\Models\Shrine;
use App\Models\VisitLog;
use Illuminate\Database\QueryException;

it('参拝記録は神社と、寄ったお店を持てる', function () {
    $log = VisitLog::factory()->for(Shop::factory())->create();

    expect($log->shrine)->toBeInstanceOf(Shrine::class)
        ->and($log->shop)->toBeInstanceOf(Shop::class)
        ->and($log->shrine->visitLogs->first()->is($log))->toBeTrue();
});

it('参拝記録が残っている神社は消せない', function () {
    $log = VisitLog::factory()->create();

    $log->shrine->delete();
})->throws(QueryException::class);

it('お店を消しても参拝記録は残り、お店が空になる', function () {
    $log = VisitLog::factory()->for(Shop::factory())->create();

    $log->shop->delete();

    expect($log->fresh()->shop_id)->toBeNull();
});

it('本文を空行で段落に分けられる', function () {
    $log = VisitLog::factory()->make(['body' => "1段落目。\n続き。\n\n2段落目。\r\n\r\n\n3段落目。\n"]);

    expect($log->paragraphs())->toBe(["1段落目。\n続き。", '2段落目。', '3段落目。']);
});

it('新しい順に並べられる', function () {
    VisitLog::factory()->create(['slug' => 'old', 'visited_on' => '2026-10-17']);
    VisitLog::factory()->create(['slug' => 'new', 'visited_on' => '2026-10-24']);

    expect(VisitLog::latestVisited()->pluck('slug')->all())->toBe(['new', 'old']);
});
