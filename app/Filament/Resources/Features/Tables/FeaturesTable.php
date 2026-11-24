<?php

declare(strict_types=1);

namespace App\Filament\Resources\Features\Tables;

use App\Models\Feature;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FeaturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('並び順')->sortable(),
                TextColumn::make('title')
                    ->label('タイトル')
                    ->description(fn (Feature $record): string => $record->summary)
                    ->searchable(['title', 'summary']),
                TextColumn::make('slug')->label('スラッグ')->searchable()->toggleable(),
                TextColumn::make('conditions')
                    ->label('条件')
                    ->state(fn (Feature $record): string => self::describeConditions($record->conditions ?? [])),
                ToggleColumn::make('is_published')->label('公開'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
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

    /**
     * 一覧で条件を読めるように、短い文にする（例：悩み career／こだわり 一人でも入りやすいお店）。
     *
     * @param  array<string, mixed>  $conditions
     */
    public static function describeConditions(array $conditions): string
    {
        $parts = [];

        if (isset($conditions['worry'])) {
            $parts[] = '悩み '.$conditions['worry'];
        }
        if (isset($conditions['area'])) {
            $parts[] = 'エリア '.$conditions['area'];
        }
        if (! empty($conditions['k'])) {
            $parts[] = 'こだわり '.implode('・', array_map(
                fn (string $k): string => Feature::KODAWARI[$k] ?? $k,
                (array) $conditions['k'],
            ));
        }
        if (isset($conditions['q'])) {
            $parts[] = 'キーワード「'.$conditions['q'].'」';
        }

        return $parts === [] ? '条件なし（すべての神社）' : implode('／', $parts);
    }
}
