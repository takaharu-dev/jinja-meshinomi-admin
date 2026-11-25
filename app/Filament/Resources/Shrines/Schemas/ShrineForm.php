<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\Schemas;

use App\Filament\Forms\SlugInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShrineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('基本情報')
                    ->description(fn (string $operation): ?string => $operation === 'create'
                        ? '保存すると編集画面に移ります。悩み・最寄り駅・参拝後のお店・写真は、そこで画面の下のタブから追加します。'
                        : null)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('名前')
                            ->placeholder('愛宕神社')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('kana')
                            ->label('読みがな')
                            ->placeholder('あたごじんじゃ')
                            ->required()
                            ->maxLength(255),
                        SlugInput::make()
                            ->placeholder('atago'),
                        Select::make('area_id')
                            ->label('エリア')
                            ->relationship('area', 'label', fn ($query) => $query->ordered())
                            ->required()
                            ->preload()
                            ->searchable(),
                        TextInput::make('address')
                            ->label('住所')
                            ->placeholder('東京都港区愛宕1-5-3')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                Section::make('紹介文')
                    ->description('一覧と詳細ページに出る文章')
                    ->schema([
                        TextInput::make('catch')
                            ->label('キャッチコピー')
                            ->placeholder('86段の「出世の石段」を登りきる')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('benefit')
                            ->label('ご利益')
                            ->placeholder('出世・仕事運')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('highlight')
                            ->label('見どころ')
                            ->helperText('空にすると、詳細ページに見どころの欄を出さない')
                            ->maxLength(255),
                        TextInput::make('after_visit')
                            ->label('参拝後の過ごし方')
                            ->placeholder('境内の茶屋で抹茶わらび餅')
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make('地酒')
                    ->relationship('sake')
                    ->columns(2)
                    ->schema([
                        Toggle::make('has_omiki')
                            ->label('御神酒がある'),
                        TextInput::make('recommended_brand')
                            ->label('おすすめの銘柄')
                            ->maxLength(255),
                    ]),
                Section::make('公開・表示')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('公開する')
                            ->helperText('オフのあいだは本体サイトに出ない'),
                        Toggle::make('has_goshuin')
                            ->label('御朱印あり'),
                        TextInput::make('popularity')
                            ->label('人気度')
                            ->helperText('「人気順」の並び替えに使う。大きいほど上')
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ]),
            ]);
    }
}
