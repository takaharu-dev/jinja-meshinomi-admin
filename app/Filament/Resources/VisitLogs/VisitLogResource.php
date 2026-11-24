<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitLogs;

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\VisitLogs\Pages\CreateVisitLog;
use App\Filament\Resources\VisitLogs\Pages\EditVisitLog;
use App\Filament\Resources\VisitLogs\Pages\ListVisitLogs;
use App\Filament\Resources\VisitLogs\Schemas\VisitLogForm;
use App\Filament\Resources\VisitLogs\Tables\VisitLogsTable;
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

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|UnitEnum|null $navigationGroup = 'コンテンツ';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

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
