<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines;

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Shrines\Pages\CreateShrine;
use App\Filament\Resources\Shrines\Pages\EditShrine;
use App\Filament\Resources\Shrines\Pages\ListShrines;
use App\Filament\Resources\Shrines\RelationManagers\ShopsRelationManager;
use App\Filament\Resources\Shrines\RelationManagers\StationsRelationManager;
use App\Filament\Resources\Shrines\RelationManagers\WorriesRelationManager;
use App\Filament\Resources\Shrines\Schemas\ShrineForm;
use App\Filament\Resources\Shrines\Tables\ShrinesTable;
use App\Models\Shrine;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ShrineResource extends Resource
{
    protected static ?string $model = Shrine::class;

    protected static ?string $modelLabel = '神社';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'コンテンツ';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    /**
     * グローバル検索（画面上部の検索）で探す項目。
     *
     * @return list<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'kana', 'slug'];
    }

    public static function form(Schema $schema): Schema
    {
        return ShrineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShrinesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            WorriesRelationManager::class,
            StationsRelationManager::class,
            ShopsRelationManager::class,
            PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShrines::route('/'),
            'create' => CreateShrine::route('/create'),
            'edit' => EditShrine::route('/{record}/edit'),
        ];
    }
}
