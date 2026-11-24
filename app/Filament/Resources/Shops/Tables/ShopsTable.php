<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('店名')->searchable(),
                TextColumn::make('genre')->label('ジャンル')->searchable(),
                TextColumn::make('budget')->label('予算'),
                IconColumn::make('has_sake')->label('日本酒')->boolean(),
                IconColumn::make('solo_friendly')->label('一人で')->boolean(),
                TextColumn::make('shrines_count')->counts('shrines')->label('神社'),
                TextColumn::make('photos_count')->counts('photos')->label('写真')->suffix('枚'),
            ])
            ->defaultSort('name')
            ->filters([
                TernaryFilter::make('has_sake')->label('日本酒が飲める'),
                TernaryFilter::make('solo_friendly')->label('一人でも入りやすい'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
