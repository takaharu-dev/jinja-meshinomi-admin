<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * 本体（jinja-meshinomi）に直書きされていたデータを入れる。
 *
 * 管理者ユーザーは入れない（パスワードを Seeder に書かないため）。`artisan admin:create` で作る。
 * 参拝記録も入れない（本体の参拝記録は本文・写真・お店がすべて仮置きだったため）。管理画面で書く。
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            MasterSeeder::class,
            ShrineSeeder::class,
            FeatureSeeder::class,
        ]);
    }
}
