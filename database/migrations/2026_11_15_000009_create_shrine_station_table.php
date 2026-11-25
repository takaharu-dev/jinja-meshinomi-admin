<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shrine_station', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shrine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('station_id')->constrained()->restrictOnDelete();
            // 「駅から徒歩5分以内」は最寄り駅のこの値で判定する（カラムは持たない）
            $table->unsignedTinyInteger('walk_minutes')->nullable()->comment('徒歩分数。分からなければ null');
            $table->boolean('is_nearest')->default(false)->comment('最寄り駅。神社ごとに1つまで');
            $table->unsignedSmallInteger('sort_order')->default(0)->comment('最寄り駅一覧の表示順');
            $table->timestamps();

            // 最寄り駅は神社ごとに1つまで。is_nearest の行だけ shrine_id が入る仮想カラムに unique を張る
            // （TiDB Cloud では CASE WHEN の関数インデックスが使えないため生成カラムで表現する）
            $table->unsignedBigInteger('nearest_shrine_id')->nullable()
                ->virtualAs('CASE WHEN is_nearest THEN shrine_id END')
                ->comment('is_nearest の行だけ shrine_id。一意制約用');

            $table->unique(['shrine_id', 'station_id']);
            $table->unique('nearest_shrine_id', 'shrine_station_nearest_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shrine_station');
    }
};
