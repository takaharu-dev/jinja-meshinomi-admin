<?php

declare(strict_types=1);

namespace App\Filament\Resources\Areas\Schemas;

use App\Filament\Forms\SlugInput;
use App\Filament\Forms\SortOrderInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('エリア')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->label('表示名')
                            ->placeholder('港区')
                            ->required()
                            ->maxLength(255),
                        SlugInput::make(),
                        Select::make('area_group_id')
                            ->label('エリアグループ')
                            ->relationship('group', 'name', fn ($query) => $query->ordered())
                            ->required()
                            ->preload(),
                        SortOrderInput::make(),
                    ]),
            ]);
    }
}
