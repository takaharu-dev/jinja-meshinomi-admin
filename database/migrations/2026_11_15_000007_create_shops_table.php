<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 参拝後に寄るお店。神社・参拝記録から使い回す
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('genre')->comment('例：蕎麦・日本酒');
            $table->string('budget')->comment('表示用の予算（例：3,000円〜）');
            $table->boolean('has_sake')->default(false)->comment('日本酒が飲める');
            $table->boolean('solo_friendly')->default(false)->comment('一人でも入りやすい');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
