<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worries', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('URL に使う ID（例：career）');
            $table->string('label')->comment('表示名（例：仕事・出世）');
            $table->string('icon', 1)->comment('アイコンに出す1文字（例：出）');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worries');
    }
};
