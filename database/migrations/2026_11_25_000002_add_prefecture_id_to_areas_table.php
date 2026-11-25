<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * エリア（市区町村）を都道府県に属させる。URL は /{都道府県}/{市区町村}/{神社} になるので、
 * エリアのスラッグは都道府県の中で重複しなければよい（例：神奈川の midori と 横浜の midori）。
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->foreignId('prefecture_id')->nullable()->after('id')->constrained()->restrictOnDelete();
        });

        // これまでのエリアは東京23区（と武蔵野市）だけなので、東京都に入れる
        if (DB::table('areas')->exists()) {
            $tokyoId = DB::table('prefectures')->where('slug', 'tokyo')->value('id')
                ?? DB::table('prefectures')->insertGetId([
                    'slug' => 'tokyo', 'label' => '東京都', 'sort_order' => 0,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            DB::table('areas')->whereNull('prefecture_id')->update(['prefecture_id' => $tokyoId]);
        }

        Schema::table('areas', function (Blueprint $table) {
            $table->foreignId('prefecture_id')->nullable(false)->change();
            $table->dropUnique(['slug']);
            $table->unique(['prefecture_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropUnique(['prefecture_id', 'slug']);
            $table->unique('slug');
            $table->dropConstrainedForeignId('prefecture_id');
        });
    }
};
