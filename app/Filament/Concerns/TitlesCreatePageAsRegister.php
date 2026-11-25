<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

/**
 * 作成画面の見出しを「神社 作成」ではなく「神社を登録」にする。
 * 一覧の右上のボタン（「神社を登録」）と言葉を揃える。
 */
trait TitlesCreatePageAsRegister
{
    public function getTitle(): string
    {
        return static::getResource()::getModelLabel().'を登録';
    }
}
