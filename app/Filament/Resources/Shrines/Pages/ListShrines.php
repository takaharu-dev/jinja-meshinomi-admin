<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\Pages;

use App\Filament\Resources\Shrines\ShrineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShrines extends ListRecords
{
    protected static string $resource = ShrineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
