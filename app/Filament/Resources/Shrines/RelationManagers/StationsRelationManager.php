<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\RelationManagers;

use App\Models\Shrine;
use App\Models\Station;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * 神社の最寄り駅。最寄り駅（is_nearest）は神社ごとに1つまで（DB の unique）なので、
 * 切り替えは Shrine::setNearestStation() に任せる。
 */
class StationsRelationManager extends RelationManager
{
    protected static string $relationship = 'stations';

    protected static ?string $title = '最寄り駅';

    protected static ?string $modelLabel = '駅';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            self::walkMinutesInput(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')->label('駅'),
                TextColumn::make('walk_minutes')
                    ->label('徒歩')
                    ->suffix('分')
                    ->placeholder('不明'),
                IconColumn::make('is_nearest')->label('最寄り')->boolean(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('駅を追加')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name'])
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect()->label('駅'),
                        self::walkMinutesInput(),
                        // pivot に直接入れると unique に引っかかるので、追加のあとで切り替える
                        Toggle::make('make_nearest')
                            ->label('最寄り駅にする')
                            ->helperText('最初の1つは、オフでも自動で最寄り駅になる'),
                    ])
                    ->after(function (array $data, Station $record): void {
                        $shrine = $this->getShrine();
                        $hasNearest = $shrine->stations()->wherePivot('is_nearest', true)->exists();

                        if (($data['make_nearest'] ?? false) || ! $hasNearest) {
                            $shrine->setNearestStation($record);
                        }
                    }),
            ])
            ->recordActions([
                Action::make('makeNearest')
                    ->label('最寄りにする')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->hidden(fn (Station $record): bool => (bool) $record->pivot->is_nearest)
                    ->action(fn (Station $record) => $this->getShrine()->setNearestStation($record)),
                EditAction::make()->label('徒歩分数'),
                DetachAction::make()->label('外す'),
            ]);
    }

    private static function walkMinutesInput(): TextInput
    {
        return TextInput::make('walk_minutes')
            ->label('駅からの徒歩分数')
            ->helperText('分からなければ空のまま。最寄り駅から5分以内だと「駅から徒歩5分以内」に入る')
            ->integer()
            ->minValue(0)
            ->maxValue(255)
            ->suffix('分');
    }

    private function getShrine(): Shrine
    {
        /** @var Shrine */
        return $this->getOwnerRecord();
    }
}
