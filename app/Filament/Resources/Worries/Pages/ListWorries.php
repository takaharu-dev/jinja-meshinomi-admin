<?php

declare(strict_types=1);

namespace App\Filament\Resources\Worries\Pages;

use App\Filament\Resources\Worries\WorryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorries extends ListRecords
{
    protected static string $resource = WorryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
