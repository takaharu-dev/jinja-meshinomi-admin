<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitLogs\Schemas;

use App\Filament\Forms\SlugInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('参拝記録')
                    ->description(fn (string $operation): ?string => $operation === 'create'
                        ? '写真は、保存したあとの編集画面で、画面の下の「写真」タブから追加します。'
                        : null)
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('タイトル')
                            ->placeholder('日枝神社 → 赤坂で軽く一杯')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        DatePicker::make('visited_on')
                            ->label('参拝した日')
                            ->native(false)
                            ->displayFormat('Y年n月j日')
                            ->maxDate(now())
                            ->required(),
                        SlugInput::make()
                            ->placeholder('2026-10-24-hie')
                            ->helperText('小文字の英数字とハイフンだけ。「参拝した日-神社」の形にそろえる（例：2026-10-24-hie）'),
                        Select::make('shrine_id')
                            ->label('参拝した神社')
                            ->relationship('shrine', 'name')
                            ->searchable(['name', 'kana'])
                            ->preload()
                            ->required(),
                        Select::make('shop_id')
                            ->label('寄ったお店')
                            ->relationship('shop', 'name')
                            ->searchable(['name', 'genre'])
                            ->preload()
                            ->placeholder('寄っていない'),
                        Textarea::make('body')
                            ->label('本文')
                            ->helperText('段落は空行で区切る')
                            ->required()
                            ->rows(12)
                            ->columnSpanFull(),
                    ]),
                Section::make('公開')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('公開する')
                            ->helperText('オフのあいだは本体サイトに出ない。写真は保存したあとの画面で追加する'),
                    ]),
            ]);
    }
}
