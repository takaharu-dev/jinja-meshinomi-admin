<?php

declare(strict_types=1);

namespace App\Filament\Forms;

use Filament\Forms\Components\TextInput;

final class SortOrderInput
{
    public static function make(string $name = 'sort_order'): TextInput
    {
        return TextInput::make($name)
            ->label('並び順')
            ->helperText('小さい順に並ぶ。一覧画面のドラッグでも並べ替えられる')
            ->integer()
            ->minValue(0)
            ->maxValue(65535)
            ->default(0)
            ->required();
    }
}
