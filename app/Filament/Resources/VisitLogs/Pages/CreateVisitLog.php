<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitLogs\Pages;

use App\Filament\Concerns\TitlesCreatePageAsRegister;
use App\Filament\Resources\VisitLogs\VisitLogResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateVisitLog extends CreateRecord
{
    use TitlesCreatePageAsRegister;

    protected static string $resource = VisitLogResource::class;

    /**
     * 保存すると編集画面に移る。紐づけ・写真はそこで追加することを伝える。
     */
    protected function getCreatedNotification(): ?Notification
    {
        return parent::getCreatedNotification()?->body('下の「写真」タブで写真を追加できます。');
    }
}
