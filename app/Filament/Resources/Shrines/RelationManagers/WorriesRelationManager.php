<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\RelationManagers;

use App\Models\Shrine;
use App\Models\Worry;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * 神社の悩み。メインの悩みは神社ごとに1つまで（DB の unique）なので、
 * 切り替えは Shrine::setPrimaryWorry() に任せる。
 */
class WorriesRelationManager extends RelationManager
{
    protected static string $relationship = 'worries';

    protected static ?string $title = '悩み';

    protected static ?string $modelLabel = '悩み';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('sort_order'))
            ->columns([
                TextColumn::make('label')->label('悩み'),
                IconColumn::make('is_primary')->label('メイン')->boolean(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('悩みを追加')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->orderBy('sort_order'))
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect()->label('悩み'),
                        // pivot のカラムではないので attach には渡らない。追加のあとで切り替える
                        Toggle::make('make_primary')
                            ->label('メインの悩みにする')
                            ->helperText('最初の1つは、オフでも自動でメインになる'),
                    ])
                    ->after(function (array $data, Worry $record): void {
                        $shrine = $this->getShrine();
                        $hasPrimary = $shrine->worries()->wherePivot('is_primary', true)->exists();

                        if (($data['make_primary'] ?? false) || ! $hasPrimary) {
                            $shrine->setPrimaryWorry($record);
                        }
                    }),
            ])
            ->recordActions([
                Action::make('makePrimary')
                    ->label('メインにする')
                    ->icon(Heroicon::OutlinedStar)
                    ->hidden(fn (Worry $record): bool => (bool) $record->pivot->is_primary)
                    ->action(fn (Worry $record) => $this->getShrine()->setPrimaryWorry($record)),
                DetachAction::make()->label('外す'),
            ]);
    }

    private function getShrine(): Shrine
    {
        /** @var Shrine */
        return $this->getOwnerRecord();
    }
}
