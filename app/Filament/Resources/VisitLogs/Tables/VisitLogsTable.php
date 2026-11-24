<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitLogs\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VisitLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['shrine', 'shop'])->withCount('photos'))
            ->columns([
                TextColumn::make('visited_on')->label('参拝した日')->date('Y/n/j')->sortable(),
                TextColumn::make('title')->label('タイトル')->searchable(),
                TextColumn::make('shrine.name')->label('神社'),
                TextColumn::make('shop.name')->label('お店')->placeholder('—'),
                TextColumn::make('photos_count')->label('写真')->suffix('枚'),
                ToggleColumn::make('is_published')->label('公開'),
            ])
            ->defaultSort(fn (Builder $query) => $query->orderByDesc('visited_on')->orderByDesc('id'))
            ->filters([
                SelectFilter::make('shrine_id')
                    ->label('神社')
                    ->relationship('shrine', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_published')
                    ->label('公開状態')
                    ->trueLabel('公開中')
                    ->falseLabel('非公開'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
