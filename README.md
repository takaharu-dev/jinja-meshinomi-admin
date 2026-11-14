# jinja-meshinomi-admin
「神社めし呑み」の管理画面。神社と、参拝後のごはん・地酒スポットを登録・管理する（Laravel / MySQL）

## ローカルでの起動

Docker と PHP 8.3+ / Composer が必要です（Laravel Sail を使います）。

```sh
composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

初回の `sail up` はアプリ用イメージのビルドに 10〜20 分ほどかかります（2回目以降はキャッシュで数秒）。

http://localhost:8000/admin で管理画面が開きます。

```sh
./vendor/bin/sail test       # テスト（Pest）
./vendor/bin/sail composer lint  # Lint（Laravel Pint）
```
