<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\MissingAttributeException;

it('$fillable にない属性への一括代入は例外になる', function () {
    new User(['is_admin' => true]);
})->throws(MassAssignmentException::class);

it('取得していない属性を参照すると例外になる', function () {
    User::factory()->create();

    User::query()->select('id')->first()->email;
})->throws(MissingAttributeException::class);
