<?php

declare(strict_types=1);

namespace App\Filament\Resources\AreaGroups\Schemas;

use App\Filament\Forms\SortOrderInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AreaGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('エリアグループ')
                    ->description('トップページでエリアをまとめて出すときの見出し')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('名前')
                            ->placeholder('都心')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        SortOrderInput::make(),
                    ]),
            ]);
    }
}
