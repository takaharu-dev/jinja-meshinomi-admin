<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shrine_shop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shrine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->restrictOnDelete();
            $table->string('distance')->comment('神社からの距離（表示用。例：徒歩3分）');
            $table->string('recommend')->comment('この神社の帰りに頼みたいもの');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['shrine_id', 'shop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shrine_shop');
    }
};
