<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 神社詳細の「こんな日に行きたい」に出す、悩みごとの文章
        Schema::create('worry_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worry_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worry_days');
    }
};
