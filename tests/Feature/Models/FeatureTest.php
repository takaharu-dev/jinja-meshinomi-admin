<?php

declare(strict_types=1);

use App\Models\Feature;

it('検索条件を本体の /api/shrines と同じ形の JSON で持てる', function () {
    $feature = Feature::factory()->create(['conditions' => ['worry' => 'career', 'k' => ['solo', 'sake']]]);

    // MySQL の JSON 型はキーの順番を保たないので、順番を問わず比べる
    expect($feature->fresh()->conditions)->toEqualCanonicalizing(['worry' => 'career', 'k' => ['solo', 'sake']]);
});

it('is_numbered のときはタイトルの %d に件数が入る', function () {
    $numbered = Feature::factory()->make(['title' => '仕事でモヤモヤした日に行きたい神社%d選', 'is_numbered' => true]);
    $plain = Feature::factory()->make(['title' => '一人で行ける神社と一杯', 'is_numbered' => false]);

    expect($numbered->displayTitle(5))->toBe('仕事でモヤモヤした日に行きたい神社5選')
        ->and($plain->displayTitle(5))->toBe('一人で行ける神社と一杯');
});

it('公開中の特集を sort_order 順に取得できる', function () {
    Feature::factory()->published()->create(['slug' => 'second', 'sort_order' => 2]);
    Feature::factory()->published()->create(['slug' => 'first', 'sort_order' => 1]);
    Feature::factory()->create(['slug' => 'draft', 'sort_order' => 0]);

    expect(Feature::published()->ordered()->pluck('slug')->all())->toBe(['first', 'second']);
});
