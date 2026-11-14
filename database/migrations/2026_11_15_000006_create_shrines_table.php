<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shrines', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('URL に使う ID（例：atago）');
            $table->string('name');
            $table->string('kana')->comment('読みがな');
            $table->foreignId('area_id')->constrained()->restrictOnDelete();
            $table->string('catch')->comment('一覧・詳細に出すキャッチコピー');
            $table->string('benefit')->comment('ご利益（表示用）');
            $table->string('address');
            $table->string('highlight')->nullable()->comment('見どころ。null なら詳細ページに出さない');
            $table->string('after_visit')->comment('参拝後の過ごし方（例：近くの蕎麦屋で一杯）');
            $table->unsignedInteger('popularity')->default(0)->comment('人気順の並び替えに使う');
            $table->boolean('has_goshuin')->default(false)->comment('御朱印あり');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shrines');
    }
};
