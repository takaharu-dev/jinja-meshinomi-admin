<?php

declare(strict_types=1);

namespace App\Filament\Resources\Stations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('駅')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('駅名')
                            ->placeholder('神谷町駅')
                            ->helperText('「駅」まで含めて入力する')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('area_id')
                            ->label('エリア')
                            ->relationship('area', 'label', fn ($query) => $query->ordered())
                            ->required()
                            ->preload(),
                    ]),
            ]);
    }
}
