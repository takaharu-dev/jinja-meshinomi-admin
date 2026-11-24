<?php

declare(strict_types=1);

namespace App\Filament\Resources\Features\Schemas;

use App\Filament\Forms\SlugInput;
use App\Filament\Forms\SortOrderInput;
use App\Models\Area;
use App\Models\Feature;
use App\Models\Worry;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class FeatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('記事')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('タイトル')
                            ->placeholder('仕事でモヤモヤした日に行きたい神社%d選')
                            ->helperText('「件数を入れる」をオンにすると、%d に当てはまる神社の数が入る')
                            ->required()
                            ->maxLength(255)
                            ->rule(fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                if ($get('is_numbered') && ! str_contains((string) $value, '%d')) {
                                    $fail('件数を入れるときは、タイトルに %d を入れてください。');
                                }
                            })
                            ->columnSpanFull(),
                        Toggle::make('is_numbered')
                            ->label('タイトルに件数を入れる')
                            ->live(),
                        SlugInput::make()
                            ->placeholder('career'),
                        TextInput::make('summary')
                            ->label('一覧に出す説明')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('lead')
                            ->label('導入文')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                Section::make('並べる神社の条件')
                    ->description('本体の「神社を探す」と同じ条件。当てはまる公開中の神社が自動で並ぶ。空の条件は使わない')
                    ->columns(2)
                    ->schema([
                        Select::make('conditions.worry')
                            ->label('悩み')
                            ->options(fn (): array => Worry::query()->ordered()->pluck('label', 'slug')->all())
                            ->placeholder('指定しない'),
                        Select::make('conditions.area')
                            ->label('エリア')
                            ->options(fn (): array => Area::query()->ordered()->pluck('label', 'slug')->all())
                            ->placeholder('指定しない'),
                        CheckboxList::make('conditions.k')
                            ->label('こだわり')
                            ->options(Feature::KODAWARI)
                            ->columns(2)
                            ->columnSpanFull(),
                        TextInput::make('conditions.q')
                            ->label('キーワード')
                            ->placeholder('神楽坂')
                            ->helperText('神社名・住所・紹介文などに含まれる言葉')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                Section::make('公開・表示')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('公開する')
                            ->helperText('オフのあいだは本体サイトに出ない'),
                        SortOrderInput::make()
                            ->helperText('小さい順に並ぶ。先頭の特集がトップページのメインになる'),
                    ]),
            ]);
    }

    /**
     * 保存の前に、空の条件を取り除く。
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function prepareForSave(array $data): array
    {
        $data['conditions'] = Feature::normalizeConditions((array) ($data['conditions'] ?? []));

        return $data;
    }
}
