<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\Pages;

use App\Filament\Resources\Shrines\ShrineResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShrine extends EditRecord
{
    protected static string $resource = ShrineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
