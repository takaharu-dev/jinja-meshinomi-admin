<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

#[Signature('admin:create {--name= : 表示名} {--email= : ログインに使うメールアドレス}')]
#[Description('管理画面にログインできる管理者ユーザーを作る')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('表示名');
        $email = $this->option('email') ?? $this->ask('メールアドレス');

        // シェルの履歴に残らないよう、パスワードは引数では受け取らない。
        // 環境変数 ADMIN_PASSWORD があればそれを使い、無ければ入力してもらう。
        $password = Env::get('ADMIN_PASSWORD') ?: $this->secret('パスワード（12文字以上）');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', Password::min(12)],
            ],
            attributes: ['name' => '表示名', 'email' => 'メールアドレス', 'password' => 'パスワード'],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        User::create($validator->validated());

        $this->info("管理者ユーザー {$email} を作成しました。");

        return self::SUCCESS;
    }
}
