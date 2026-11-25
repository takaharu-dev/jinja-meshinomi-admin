<?php

declare(strict_types=1);

namespace App\Filament\Resources\Prefectures\Schemas;

use App\Filament\Forms\SlugInput;
use App\Filament\Forms\SortOrderInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrefectureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('都道府県')
                    ->description('URL の1段目（/tokyo/…）。市区町村（エリア）はこの下に入る')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->label('表示名')
                            ->placeholder('東京都')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        SlugInput::make()
                            ->placeholder('tokyo')
                            ->helperText('「都」「県」「府」を付けないローマ字（例：tokyo、kanagawa、hokkaido）。URL の1段目になる'),
                        SortOrderInput::make(),
                    ]),
            ]);
    }
}
