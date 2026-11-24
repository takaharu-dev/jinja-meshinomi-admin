<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\Pages;

use App\Filament\Resources\Shrines\ShrineResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShrine extends CreateRecord
{
    protected static string $resource = ShrineResource::class;
}
