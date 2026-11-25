<?php

declare(strict_types=1);

namespace App\Filament\Resources\Worries;

use App\Filament\Resources\Worries\Pages\CreateWorry;
use App\Filament\Resources\Worries\Pages\EditWorry;
use App\Filament\Resources\Worries\Pages\ListWorries;
use App\Filament\Resources\Worries\Schemas\WorryForm;
use App\Filament\Resources\Worries\Tables\WorriesTable;
use App\Filament\Support\NavigationGroups;
use App\Models\Worry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WorryResource extends Resource
{
    protected static ?string $model = Worry::class;

    protected static ?string $modelLabel = '悩み';

    /** メニュー・一覧の見出し・パンくずに出る。「一覧」と付けて、開くと一覧が見えることを伝える */
    protected static ?string $pluralModelLabel = '悩みの一覧';

    /** 「一覧を見る」グループの中なので、メニューには名前だけを出す */
    protected static ?string $navigationLabel = '悩み';

    protected static ?string $recordTitleAttribute = 'label';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::SETTINGS;

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    public static function form(Schema $schema): Schema
    {
        return WorryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorries::route('/'),
            'create' => CreateWorry::route('/create'),
            'edit' => EditWorry::route('/{record}/edit'),
        ];
    }
}
