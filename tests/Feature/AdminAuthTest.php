<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Auth\Pages\Login;
use Livewire\Livewire;

it('正しいメールアドレスとパスワードでログインできる', function () {
    $user = User::factory()->create(['password' => 'correct-password']);

    Livewire::test(Login::class)
        ->fillForm(['email' => $user->email, 'password' => 'correct-password'])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/admin');

    $this->assertAuthenticatedAs($user);
});

it('パスワードが違うとログインできない', function () {
    $user = User::factory()->create(['password' => 'correct-password']);

    Livewire::test(Login::class)
        ->fillForm(['email' => $user->email, 'password' => 'wrong-password'])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

it('ログイン済みなら管理画面を表示できる', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk();
});

it('ユーザー登録画面とパスワード再設定画面は無い', function (string $path) {
    $this->get($path)->assertNotFound();
})->with([
    '/admin/register',
    '/admin/password-reset/request',
]);
