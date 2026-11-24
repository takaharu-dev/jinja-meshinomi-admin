<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers;

use App\Models\Photo;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * 写真（神社・参拝記録・お店で共通）。
 *
 * 保存先のディスクとフォルダは config/admin.php の photos（ADMIN_PHOTO_DISK）で決める。
 * public から s3 / R2 に切り替えても、この画面は変えなくてよい。
 * ファイルの削除は Photo モデルの deleted イベントで行う。
 */
class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    protected static ?string $title = '写真';

    protected static ?string $modelLabel = '写真';

    /** アップロードできる大きさの上限（KB）。スマホの写真がそのまま入る大きさにする */
    public const int MAX_SIZE_KB = 10240;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('path')
                ->label('写真')
                ->image()
                ->disk(config('admin.photos.disk'))
                ->directory(config('admin.photos.directory'))
                ->visibility('public')
                ->maxSize(self::MAX_SIZE_KB)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                // 差し替えたときに古いファイルが残らないようにする
                ->deleteUploadedFileUsing(fn (string $file) => Photo::disk()->delete($file))
                ->required()
                ->columnSpanFull(),
            TextInput::make('caption')
                ->label('説明')
                ->helperText('画像の alt にも使う。何が写っているかを短く（例：愛宕神社の出世の石段）')
                ->maxLength(255)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('path')
                    ->label('写真')
                    ->disk(config('admin.photos.disk'))
                    ->imageHeight(64),
                TextColumn::make('caption')->label('説明')->placeholder('（説明なし）')->wrap(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('写真を追加')
                    // 新しい写真は最後に並べる
                    ->mutateDataUsing(fn (array $data): array => [
                        ...$data,
                        'sort_order' => (int) $this->getOwnerRecord()->photos()->max('sort_order') + 1,
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
