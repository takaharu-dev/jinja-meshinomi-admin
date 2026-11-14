<?php

declare(strict_types=1);

namespace App\Filament\Resources\Worries\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('並び順')->sortable(),
                TextColumn::make('icon')->label('アイコン'),
                TextColumn::make('label')->label('表示名')->searchable(),
                TextColumn::make('slug')->label('スラッグ')->searchable(),
                TextColumn::make('scenes_count')->counts('scenes')->label('こんな日に行きたい'),
                TextColumn::make('shrines_count')->counts('shrines')->label('神社'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
