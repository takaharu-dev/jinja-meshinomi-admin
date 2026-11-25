<?php

declare(strict_types=1);

namespace App\Filament\Resources\Prefectures\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrefecturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('並び順')->sortable(),
                TextColumn::make('label')->label('表示名')->searchable(),
                TextColumn::make('slug')->label('スラッグ')->searchable(),
                TextColumn::make('areas_count')->counts('areas')->label('エリア'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
