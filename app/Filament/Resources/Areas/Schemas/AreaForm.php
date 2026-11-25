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
                        Select::make('prefecture_id')
                            ->label('都道府県')
                            ->relationship('prefecture', 'label', fn ($query) => $query->ordered())
                            ->required()
                            ->preload()
                            ->live(),
                        TextInput::make('label')
                            ->label('表示名（市区町村）')
                            ->placeholder('港区')
                            ->required()
                            ->maxLength(255),
                        SlugInput::make(scope: 'prefecture_id')
                            ->placeholder('minato')
                            ->helperText('市区町村名のローマ字（例：minato、kamakura）。URL の2段目（/tokyo/minato/…）になる。同じ都道府県の中で重ならなければよい'),
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
