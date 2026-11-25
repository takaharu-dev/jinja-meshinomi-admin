<?php

declare(strict_types=1);

namespace App\Filament\Resources\Areas;

use App\Filament\Resources\Areas\Pages\CreateArea;
use App\Filament\Resources\Areas\Pages\EditArea;
use App\Filament\Resources\Areas\Pages\ListAreas;
use App\Filament\Resources\Areas\Schemas\AreaForm;
use App\Filament\Resources\Areas\Tables\AreasTable;
use App\Filament\Support\NavigationGroups;
use App\Models\Area;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $modelLabel = 'エリア';

    /** メニュー・一覧の見出し・パンくずに出る。「一覧」と付けて、開くと一覧が見えることを伝える */
    protected static ?string $pluralModelLabel = 'エリアの一覧';

    /** 「一覧を見る」グループの中なので、メニューには名前だけを出す */
    protected static ?string $navigationLabel = 'エリア';

    protected static ?string $recordTitleAttribute = 'label';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::SETTINGS;

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    public static function form(Schema $schema): Schema
    {
        return AreaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AreasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAreas::route('/'),
            'create' => CreateArea::route('/create'),
            'edit' => EditArea::route('/{record}/edit'),
        ];
    }
}
