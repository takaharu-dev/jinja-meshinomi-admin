<?php

declare(strict_types=1);

use App\Models\Worry;
use App\Models\WorryDay;
use Illuminate\Database\UniqueConstraintViolationException;

it('こんな日に行きたい を sort_order 順に取得できる', function () {
    $worry = Worry::factory()->create();
    WorryDay::factory()->for($worry)->create(['text' => '2番目', 'sort_order' => 2]);
    WorryDay::factory()->for($worry)->create(['text' => '1番目', 'sort_order' => 1]);

    expect($worry->days->pluck('text')->all())->toBe(['1番目', '2番目']);
});

it('悩みを消すと こんな日に行きたい も消える', function () {
    $day = WorryDay::factory()->create();

    $day->worry->delete();

    expect(WorryDay::find($day->id))->toBeNull();
});

it('slug は重複できない', function () {
    Worry::factory()->create(['slug' => 'career']);

    Worry::factory()->create(['slug' => 'career']);
})->throws(UniqueConstraintViolationException::class);

it('悩みを sort_order 順に並べられる', function () {
    Worry::factory()->create(['slug' => 'b', 'sort_order' => 2]);
    Worry::factory()->create(['slug' => 'a', 'sort_order' => 1]);

    expect(Worry::ordered()->pluck('slug')->all())->toBe(['a', 'b']);
});
