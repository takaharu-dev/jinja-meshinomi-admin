<?php

declare(strict_types=1);

namespace App\Filament\Forms;

use Filament\Forms\Components\TextInput;

/**
 * URL に使う slug の入力欄。本体の URL（/shrines/atago など）にそのまま使うので、
 * 小文字の英数字とハイフンだけにする。
 */
final class SlugInput
{
    public const string PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public static function make(string $name = 'slug'): TextInput
    {
        return TextInput::make($name)
            ->label('スラッグ（URL）')
            ->helperText('小文字の英数字とハイフンだけ（例：tokyo-daijingu）。URL に使うので、公開後は変えない')
            ->required()
            ->maxLength(255)
            ->regex(self::PATTERN)
            ->unique(ignoreRecord: true);
    }
}
