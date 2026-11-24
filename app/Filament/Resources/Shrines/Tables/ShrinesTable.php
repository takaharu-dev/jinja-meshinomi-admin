<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\Tables;

use App\Models\Shrine;
use App\Models\Worry;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShrinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // メインの悩み・写真の枚数を一覧で出すので、まとめて読み込む（N+1 を防ぐ）
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['area', 'worries'])->withCount('photos'))
            ->columns([
                TextColumn::make('name')
                    ->label('名前')
                    ->description(fn (Shrine $record): string => $record->kana)
                    ->searchable(['name', 'kana']),
                TextColumn::make('slug')->label('スラッグ')->searchable()->toggleable(),
                TextColumn::make('area.label')->label('エリア'),
                TextColumn::make('primary_worry')
                    ->label('メインの悩み')
                    ->state(fn (Shrine $record): ?string => $record->primaryWorry()?->label)
                    ->placeholder('未設定'),
                TextColumn::make('photos_count')->label('写真')->suffix('枚'),
                IconColumn::make('has_goshuin')->label('御朱印')->boolean()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('popularity')->label('人気度')->sortable()->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_published')->label('公開'),
                TextColumn::make('updated_at')->label('更新')->since()->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                // relationship() に並び順を渡すと whereHas の中で id が曖昧になるので、選択肢と条件を分けて書く
                SelectFilter::make('worries')
                    ->label('悩み')
                    ->options(fn (): array => Worry::query()->ordered()->pluck('label', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $q, $worryId) => $q->whereHas('worries', fn (Builder $w) => $w->whereKey($worryId)),
                    )),
                SelectFilter::make('area_id')
                    ->label('エリア')
                    ->relationship('area', 'label', fn ($query) => $query->ordered())
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
