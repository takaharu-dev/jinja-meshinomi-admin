<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops;

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Shops\Pages\CreateShop;
use App\Filament\Resources\Shops\Pages\EditShop;
use App\Filament\Resources\Shops\Pages\ListShops;
use App\Filament\Resources\Shops\Schemas\ShopForm;
use App\Filament\Resources\Shops\Tables\ShopsTable;
use App\Models\Shop;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;

    protected static ?string $modelLabel = 'お店';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'コンテンツ';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    public static function form(Schema $schema): Schema
    {
        return ShopForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShopsTable::configure($table);
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
            'index' => ListShops::route('/'),
            'create' => CreateShop::route('/create'),
            'edit' => EditShop::route('/{record}/edit'),
        ];
    }
}
