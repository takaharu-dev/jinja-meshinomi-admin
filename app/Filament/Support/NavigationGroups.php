<?php

declare(strict_types=1);

namespace App\Filament\Support;

/**
 * 左のメニューのグループ名。「何をしたいか」で分ける。
 */
final class NavigationGroups
{
    /** 登録画面への近道 */
    public const string REGISTER = '＋ 登録する';

    /** 一覧画面 */
    public const string LISTS = '一覧を見る';

    /** 悩み・エリア・駅などのマスタ。最初に整えたら、ふだんは触らない */
    public const string SETTINGS = '設定（ふだんは触らない）';
}
