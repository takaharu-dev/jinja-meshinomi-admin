<?php

declare(strict_types=1);

namespace App\Filament\Resources\Prefectures\Pages;

use App\Filament\Resources\Prefectures\PrefectureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrefecture extends EditRecord
{
    protected static string $resource = PrefectureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
