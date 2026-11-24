<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('お店')
                    ->description('神社からの距離・おすすめは、神社の編集画面の「参拝後のお店」で神社ごとに入れる')
                    ->columns(2)
                    ->schema(self::fields()),
            ]);
    }

    /**
     * お店そのものの入力欄。神社の編集画面からお店を作るときにも使う。
     *
     * @return list<TextInput|Toggle>
     */
    public static function fields(): array
    {
        return [
            TextInput::make('name')
                ->label('店名')
                ->required()
                ->maxLength(255),
            TextInput::make('genre')
                ->label('ジャンル')
                ->placeholder('蕎麦・日本酒')
                ->required()
                ->maxLength(255),
            TextInput::make('budget')
                ->label('予算')
                ->placeholder('3,000円〜')
                ->required()
                ->maxLength(255),
            Toggle::make('has_sake')
                ->label('日本酒が飲める')
                ->inline(false),
            Toggle::make('solo_friendly')
                ->label('一人でも入りやすい')
                ->inline(false),
        ];
    }
}
