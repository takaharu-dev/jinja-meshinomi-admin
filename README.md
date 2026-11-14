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

管理者ユーザーは artisan コマンドで作ります（ユーザー登録画面はありません）。パスワードは環境変数 `ADMIN_PASSWORD` か、未設定なら対話で入力します。

```sh
./vendor/bin/sail artisan admin:create
```

### IP 制限

管理画面は、ログインに加えて IP アドレスでも制限しています。

| 環境変数 | 内容 |
|---|---|
| `ADMIN_IP_RESTRICTION_ENABLED` | `false` で無効（ローカル用）。未設定なら有効 |
| `ADMIN_ALLOWED_IPS` | 許可する IP をカンマ区切りで指定。CIDR（`198.51.100.0/24`）と IPv6 も可。空なら全部拒否 |

```sh
./vendor/bin/sail test       # テスト（Pest）
./vendor/bin/sail composer lint  # Lint（Laravel Pint）
```
