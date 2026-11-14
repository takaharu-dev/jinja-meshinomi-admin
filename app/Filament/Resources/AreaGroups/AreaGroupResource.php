<?php

declare(strict_types=1);

namespace App\Filament\Resources\AreaGroups;

use App\Filament\Resources\AreaGroups\Pages\CreateAreaGroup;
use App\Filament\Resources\AreaGroups\Pages\EditAreaGroup;
use App\Filament\Resources\AreaGroups\Pages\ListAreaGroups;
use App\Filament\Resources\AreaGroups\Schemas\AreaGroupForm;
use App\Filament\Resources\AreaGroups\Tables\AreaGroupsTable;
use App\Models\AreaGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AreaGroupResource extends Resource
{
    protected static ?string $model = AreaGroup::class;

    protected static ?string $modelLabel = 'エリアグループ';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'マスタ';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    public static function form(Schema $schema): Schema
    {
        return AreaGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AreaGroupsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAreaGroups::route('/'),
            'create' => CreateAreaGroup::route('/create'),
            'edit' => EditAreaGroup::route('/{record}/edit'),
        ];
    }
}
