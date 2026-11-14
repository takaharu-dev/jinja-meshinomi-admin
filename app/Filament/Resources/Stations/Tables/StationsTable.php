<?php

declare(strict_types=1);

namespace App\Filament\Resources\Stations\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('駅名')->searchable()->sortable(),
                TextColumn::make('area.label')->label('エリア'),
                TextColumn::make('shrines_count')->counts('shrines')->label('神社'),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('area_id')
                    ->label('エリア')
                    ->relationship('area', 'label', fn ($query) => $query->ordered())
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
