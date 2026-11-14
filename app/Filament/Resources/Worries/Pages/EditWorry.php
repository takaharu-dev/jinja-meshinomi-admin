<?php

declare(strict_types=1);

namespace App\Filament\Resources\Worries\Pages;

use App\Filament\Resources\Worries\WorryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorry extends EditRecord
{
    protected static string $resource = WorryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
