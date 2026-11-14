<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 神社の地酒情報。神社と1対1
        Schema::create('shrine_sakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shrine_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('has_omiki')->default(false)->comment('御神酒がある');
            $table->string('recommended_brand')->nullable()->comment('おすすめの銘柄');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shrine_sakes');
    }
};
