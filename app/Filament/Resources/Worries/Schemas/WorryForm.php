<?php

declare(strict_types=1);

namespace App\Filament\Resources\Worries\Schemas;

use App\Filament\Forms\SlugInput;
use App\Filament\Forms\SortOrderInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('悩み')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->label('表示名')
                            ->placeholder('仕事・出世')
                            ->required()
                            ->maxLength(255),
                        SlugInput::make(),
                        TextInput::make('icon')
                            ->label('アイコン（1文字）')
                            ->placeholder('出')
                            ->required()
                            ->maxLength(1),
                        SortOrderInput::make(),
                    ]),
                Section::make('こんな日に行きたい')
                    ->description('神社の詳細ページに出る文章。メインの悩みがこの悩みの神社に表示される')
                    ->schema([
                        Repeater::make('scenes')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->simple(
                                TextInput::make('text')
                                    ->label('文章')
                                    ->required()
                                    ->maxLength(255),
                            )
                            ->defaultItems(0)
                            ->addActionLabel('文章を追加'),
                    ]),
            ]);
    }
}
