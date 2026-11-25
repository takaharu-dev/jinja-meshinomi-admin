<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 神社の URL は /{都道府県}/{市区町村}/{神社} になるので、神社のスラッグはエリアの中で重複しなければよい
 * （例：saitama/kawagoe/hikawa-jinja と saitama/omiya/hikawa-jinja）。
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shrines', function (Blueprint $table) {
            $table->unique(['area_id', 'slug']);
            $table->dropUnique(['slug']);
        });
    }

    public function down(): void
    {
        Schema::table('shrines', function (Blueprint $table) {
            $table->unique('slug');
            $table->dropUnique(['area_id', 'slug']);
        });
    }
};
