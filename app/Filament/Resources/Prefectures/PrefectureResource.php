<?php

declare(strict_types=1);

namespace App\Filament\Resources\Prefectures;

use App\Filament\Resources\Prefectures\Pages\CreatePrefecture;
use App\Filament\Resources\Prefectures\Pages\EditPrefecture;
use App\Filament\Resources\Prefectures\Pages\ListPrefectures;
use App\Filament\Resources\Prefectures\Schemas\PrefectureForm;
use App\Filament\Resources\Prefectures\Tables\PrefecturesTable;
use App\Filament\Support\NavigationGroups;
use App\Models\Prefecture;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PrefectureResource extends Resource
{
    protected static ?string $model = Prefecture::class;

    protected static ?string $modelLabel = '都道府県';

    /** メニュー・一覧の見出し・パンくずに出る。「一覧」と付けて、開くと一覧が見えることを伝える */
    protected static ?string $pluralModelLabel = '都道府県の一覧';

    /** 「一覧を見る」グループの中なので、メニューには名前だけを出す */
    protected static ?string $navigationLabel = '都道府県';

    protected static ?string $recordTitleAttribute = 'label';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::SETTINGS;

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    public static function form(Schema $schema): Schema
    {
        return PrefectureForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrefecturesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrefectures::route('/'),
            'create' => CreatePrefecture::route('/create'),
            'edit' => EditPrefecture::route('/{record}/edit'),
        ];
    }
}
