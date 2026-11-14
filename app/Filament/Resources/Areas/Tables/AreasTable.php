<?php

declare(strict_types=1);

namespace App\Filament\Resources\Areas\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AreasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('並び順')->sortable(),
                TextColumn::make('label')->label('表示名')->searchable(),
                TextColumn::make('slug')->label('スラッグ')->searchable(),
                TextColumn::make('group.name')->label('エリアグループ'),
                TextColumn::make('stations_count')->counts('stations')->label('駅'),
                TextColumn::make('shrines_count')->counts('shrines')->label('神社'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('area_group_id')
                    ->label('エリアグループ')
                    ->relationship('group', 'name', fn ($query) => $query->ordered())
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
