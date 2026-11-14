<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('URL に使う ID（例：minato）');
            $table->string('label')->comment('表示名（例：港区）');
            // エリアが残っているグループは消せないようにする
            $table->foreignId('area_group_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
