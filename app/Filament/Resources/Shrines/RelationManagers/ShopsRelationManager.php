<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\RelationManagers;

use App\Filament\Resources\Shops\Schemas\ShopForm;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * 参拝後に寄るお店。お店は独立したテーブルなので、登録済みのお店を紐づけるか、ここで新しく作る。
 * 距離とおすすめはこの神社から見た情報なので pivot（shrine_shop）に持つ。
 */
class ShopsRelationManager extends RelationManager
{
    protected static string $relationship = 'shops';

    protected static ?string $title = '参拝後のお店';

    protected static ?string $modelLabel = 'お店';

    /**
     * 編集では、この神社との紐づけ（距離・おすすめ）だけを直す。お店そのものは「お店」の画面で直す。
     */
    public function form(Schema $schema): Schema
    {
        return $schema->components(self::pivotInputs());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')->label('店名'),
                TextColumn::make('genre')->label('ジャンル'),
                TextColumn::make('distance')->label('距離'),
                TextColumn::make('recommend')->label('おすすめ')->wrap(),
                IconColumn::make('has_sake')->label('日本酒')->boolean(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('登録済みのお店を追加')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'genre'])
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect()->label('お店'),
                        ...self::pivotInputs(),
                    ]),
                CreateAction::make()
                    ->label('新しいお店を作って追加')
                    ->schema([
                        Section::make('お店')->columns(2)->schema(ShopForm::fields()),
                        Section::make('この神社から見た情報')->schema(self::pivotInputs()),
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('距離・おすすめ'),
                DetachAction::make()->label('外す'),
            ]);
    }

    /**
     * @return list<TextInput>
     */
    private static function pivotInputs(): array
    {
        return [
            TextInput::make('distance')
                ->label('神社からの距離')
                ->placeholder('徒歩3分')
                ->required()
                ->maxLength(255),
            TextInput::make('recommend')
                ->label('この神社の帰りに頼みたいもの')
                ->placeholder('鴨せいろと冷酒')
                ->required()
                ->maxLength(255),
        ];
    }
}
