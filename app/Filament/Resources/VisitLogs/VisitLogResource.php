<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitLogs;

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\VisitLogs\Pages\CreateVisitLog;
use App\Filament\Resources\VisitLogs\Pages\EditVisitLog;
use App\Filament\Resources\VisitLogs\Pages\ListVisitLogs;
use App\Filament\Resources\VisitLogs\Schemas\VisitLogForm;
use App\Filament\Resources\VisitLogs\Tables\VisitLogsTable;
use App\Filament\Support\NavigationGroups;
use App\Models\VisitLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VisitLogResource extends Resource
{
    protected static ?string $model = VisitLog::class;

    protected static ?string $modelLabel = '参拝記録';

    /** メニュー・一覧の見出し・パンくずに出る。「一覧」と付けて、開くと一覧が見えることを伝える */
    protected static ?string $pluralModelLabel = '参拝記録の一覧';

    /** 「一覧を見る」グループの中なので、メニューには名前だけを出す */
    protected static ?string $navigationLabel = '参拝記録';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::LISTS;

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    /** メニューに件数を出す */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()->count();
    }

    public static function form(Schema $schema): Schema
    {
        return VisitLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisitLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisitLogs::route('/'),
            'create' => CreateVisitLog::route('/create'),
            'edit' => EditVisitLog::route('/{record}/edit'),
        ];
    }
}
