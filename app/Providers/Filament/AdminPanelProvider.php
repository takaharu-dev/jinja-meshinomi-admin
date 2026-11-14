<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Actions\DeleteAction;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            // 色・フォント・ロゴは本体の frontend/src/styles/global.css に合わせる
            ->brandName('神社めし呑み 社務所')
            ->brandLogo(fn () => view('filament.brand-logo'))
            ->brandLogoHeight('2rem')
            ->favicon(fn () => asset('favicon.svg'))
            ->colors([
                'primary' => '#e9524e', // 朱
                'success' => '#4a6b3a', // 榊
            ])
            ->font('Noto Sans JP', provider: GoogleFontProvider::class)
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.cream-background'))
            ->navigationGroups(['コンテンツ', 'マスタ'])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        // 外部キーの restrict で消せないとき（神社に使われている悩みなど）は、
        // エラー画面ではなく通知で理由を伝える
        DeleteAction::configureUsing(fn (DeleteAction $action) => $action
            ->using(function (Model $record) use ($action): bool {
                try {
                    return (bool) $record->delete();
                } catch (QueryException $e) {
                    if (! self::isForeignKeyViolation($e)) {
                        throw $e;
                    }

                    $action->failureNotificationTitle('ほかのデータで使われているため、削除できません');

                    return false;
                }
            }));
    }

    /**
     * MySQL の「子のレコードがあるので親を消せない」エラーか。
     */
    private static function isForeignKeyViolation(QueryException $e): bool
    {
        return ($e->errorInfo[1] ?? null) === 1451;
    }
}
