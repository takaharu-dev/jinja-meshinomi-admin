<?php

declare(strict_types=1);

namespace App\Filament\Resources\Areas\Pages;

use App\Filament\Concerns\TitlesCreatePageAsRegister;
use App\Filament\Resources\Areas\AreaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArea extends CreateRecord
{
    use TitlesCreatePageAsRegister;

    protected static string $resource = AreaResource::class;
}
