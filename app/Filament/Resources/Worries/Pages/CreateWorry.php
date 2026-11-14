<?php

declare(strict_types=1);

namespace App\Filament\Resources\Worries\Pages;

use App\Filament\Resources\Worries\WorryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorry extends CreateRecord
{
    protected static string $resource = WorryResource::class;
}
