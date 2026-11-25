<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shrine_worry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shrine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worry_id')->constrained()->restrictOnDelete();
            $table->boolean('is_primary')->default(false)->comment('メインの悩み。神社ごとに1つまで');
            $table->timestamps();

            // メインの悩みは神社ごとに1つまで。is_primary の行だけ shrine_id が入る仮想カラムに unique を張る
            // （TiDB Cloud では CASE WHEN の関数インデックスが使えないため生成カラムで表現する）
            $table->unsignedBigInteger('primary_shrine_id')->nullable()
                ->virtualAs('CASE WHEN is_primary THEN shrine_id END')
                ->comment('is_primary の行だけ shrine_id。一意制約用');

            $table->unique(['shrine_id', 'worry_id']);
            $table->unique('primary_shrine_id', 'shrine_worry_primary_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shrine_worry');
    }
};
