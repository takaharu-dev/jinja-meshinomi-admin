<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('URL に使う ID（例：kagurazaka-night）');
            $table->string('title')->comment('is_numbered のときは %d に件数が入る');
            $table->boolean('is_numbered')->default(false);
            $table->string('summary');
            $table->text('lead');
            // 本体の /api/shrines と同じ検索条件。例：{"worry": "career"} / {"k": ["solo"]} / {"q": "神楽坂"}
            $table->json('conditions')->comment('worry・area・q は文字列、k は配列');
            $table->unsignedSmallInteger('sort_order')->default(0)->comment('先頭がトップページのメイン特集');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
