<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\Pages;

use App\Filament\Concerns\TitlesCreatePageAsRegister;
use App\Filament\Resources\Shrines\ShrineResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateShrine extends CreateRecord
{
    use TitlesCreatePageAsRegister;

    protected static string $resource = ShrineResource::class;

    /**
     * 保存すると編集画面に移る。紐づけ・写真はそこで追加することを伝える。
     */
    protected function getCreatedNotification(): ?Notification
    {
        return parent::getCreatedNotification()?->body('下のタブで、悩み・最寄り駅・参拝後のお店・写真を追加してください。');
    }
}
