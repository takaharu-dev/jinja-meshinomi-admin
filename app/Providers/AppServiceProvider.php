<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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
    }
}
