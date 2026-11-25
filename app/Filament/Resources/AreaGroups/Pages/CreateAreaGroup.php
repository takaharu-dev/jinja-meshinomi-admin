<?php

declare(strict_types=1);

namespace App\Filament\Resources\AreaGroups\Pages;

use App\Filament\Concerns\TitlesCreatePageAsRegister;
use App\Filament\Resources\AreaGroups\AreaGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAreaGroup extends CreateRecord
{
    use TitlesCreatePageAsRegister;

    protected static string $resource = AreaGroupResource::class;
}
