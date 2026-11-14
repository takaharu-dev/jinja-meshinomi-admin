<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

afterEach(function () {
    putenv('ADMIN_PASSWORD');
    unset($_ENV['ADMIN_PASSWORD'], $_SERVER['ADMIN_PASSWORD']);
});

function setAdminPasswordEnv(string $password): void
{
    putenv("ADMIN_PASSWORD={$password}");
    $_ENV['ADMIN_PASSWORD'] = $_SERVER['ADMIN_PASSWORD'] = $password;
}

it('環境変数のパスワードで管理者を作れる', function () {
    setAdminPasswordEnv('long-enough-password');

    $this->artisan('admin:create', ['--name' => '管理者', '--email' => 'admin@example.com'])
        ->assertSuccessful();

    $user = User::firstWhere('email', 'admin@example.com');
    expect($user->name)->toBe('管理者')
        ->and(Hash::check('long-enough-password', $user->password))->toBeTrue();
});

it('環境変数が無ければ対話で入力して作れる', function () {
    $this->artisan('admin:create')
        ->expectsQuestion('表示名', '管理者')
        ->expectsQuestion('メールアドレス', 'admin@example.com')
        ->expectsQuestion('パスワード（12文字以上）', 'long-enough-password')
        ->assertSuccessful();

    expect(User::where('email', 'admin@example.com')->exists())->toBeTrue();
});

it('12文字未満のパスワードでは作れない', function () {
    setAdminPasswordEnv('short');

    $this->artisan('admin:create', ['--name' => '管理者', '--email' => 'admin@example.com'])
        ->assertFailed();

    expect(User::count())->toBe(0);
});

it('同じメールアドレスの管理者は作れない', function () {
    User::factory()->create(['email' => 'admin@example.com']);
    setAdminPasswordEnv('long-enough-password');

    $this->artisan('admin:create', ['--name' => '管理者', '--email' => 'admin@example.com'])
        ->assertFailed();

    expect(User::count())->toBe(1);
});
