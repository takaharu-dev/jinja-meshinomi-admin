<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Shop;
use App\Models\Shrine;
use App\Models\VisitLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // N+1 の遅延読み込み・$fillable 外への代入・存在しない属性の参照を例外にする。
        // 本番で例外を出してページを止めないよう、本番以外だけで有効にする。
        Model::shouldBeStrict(! $this->app->isProduction());

        // ポリモーフィック関連（photos.photoable_type）に入る名前。
        // クラス名ではなく短い名前にして、Go からも読みやすくする。登録していないモデルは使えない。
        Relation::enforceMorphMap([
            'shrine' => Shrine::class,
            'visit_log' => VisitLog::class,
            'shop' => Shop::class,
        ]);
    }
}
