<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

            $table->unique(['shrine_id', 'worry_id']);
        });

        // メインの悩みは神社ごとに1つまで（is_primary = true の行だけを対象にした unique）
        DB::statement('CREATE UNIQUE INDEX shrine_worry_primary_unique ON shrine_worry ((CASE WHEN is_primary THEN shrine_id END))');
    }

    public function down(): void
    {
        Schema::dropIfExists('shrine_worry');
    }
};
