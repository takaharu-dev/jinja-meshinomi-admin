<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Resources\Shops\ShopResource;
use App\Filament\Resources\Shrines\ShrineResource;
use App\Filament\Resources\VisitLogs\VisitLogResource;
use App\Filament\Support\NavigationGroups;
use App\Filament\Widgets\GuideWidget;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
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
            ->navigationGroups([NavigationGroups::REGISTER, NavigationGroups::LISTS, NavigationGroups::SETTINGS])
            // よく使う登録画面への近道。一覧を開かなくても、メニューから直接登録できる
            ->navigationItems([
                self::registerLink('神社を登録', ShrineResource::class, 1),
                self::registerLink('参拝記録を書く', VisitLogResource::class, 2),
                self::registerLink('お店を登録', ShopResource::class, 3),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                GuideWidget::class,
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
        // 一覧の右上のボタンを「作成」ではなく「神社を登録」のように、何を登録するかが分かる言葉にする。
        // RelationManager のボタン（「写真を追加」など）は個別に label を付けているので、そちらが優先される
        CreateAction::configureUsing(fn (CreateAction $action) => $action
            ->label(fn (CreateAction $action): string => $action->getModelLabel().'を登録')
            ->icon(Heroicon::Plus));

        // 一覧の各行のボタンを「編集」ではなく「編集する」に。RelationManager で個別に label を付けたものはそちらが優先
        EditAction::configureUsing(fn (EditAction $action) => $action->label('編集する'));

        // データがまだないときは「〇〇が見つかりません」ではなく、次にやることを出す
        Table::configureUsing(fn (Table $table) => $table
            ->emptyStateHeading('まだ登録されていません')
            ->emptyStateDescription('右上のボタンから追加できます。'));

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
     * メニューの「＋ 登録する」に並べる、登録画面への近道。
     *
     * @param  class-string<resource>  $resource
     */
    private static function registerLink(string $label, string $resource, int $sort): NavigationItem
    {
        return NavigationItem::make($label)
            ->group(NavigationGroups::REGISTER)
            ->sort($sort)
            ->url(fn (): string => $resource::getUrl('create'))
            ->isActiveWhen(fn (): bool => request()->routeIs($resource::getRouteBaseName().'.create'));
    }

    /**
     * MySQL の「子のレコードがあるので親を消せない」エラーか。
     */
    private static function isForeignKeyViolation(QueryException $e): bool
    {
        return ($e->errorInfo[1] ?? null) === 1451;
    }
}
