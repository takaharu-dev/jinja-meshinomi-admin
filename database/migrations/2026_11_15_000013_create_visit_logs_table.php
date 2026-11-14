<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('URL に使う ID（例：2026-10-24-hie）');
            $table->date('visited_on');
            // 参拝記録が残っている神社は消せない。お店は消しても記録は残す
            $table->foreignId('shrine_id')->constrained()->restrictOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('body')->comment('本文。段落は空行で区切る');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index('visited_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
