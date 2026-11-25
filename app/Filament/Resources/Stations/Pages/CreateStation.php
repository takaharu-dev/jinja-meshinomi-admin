<?php

declare(strict_types=1);

namespace App\Filament\Resources\Stations\Pages;

use App\Filament\Concerns\TitlesCreatePageAsRegister;
use App\Filament\Resources\Stations\StationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStation extends CreateRecord
{
    use TitlesCreatePageAsRegister;

    protected static string $resource = StationResource::class;
}
