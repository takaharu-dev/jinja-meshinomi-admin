<?php

declare(strict_types=1);

namespace App\Filament\Resources\Prefectures\Pages;

use App\Filament\Concerns\TitlesCreatePageAsRegister;
use App\Filament\Resources\Prefectures\PrefectureResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrefecture extends CreateRecord
{
    use TitlesCreatePageAsRegister;

    protected static string $resource = PrefectureResource::class;
}
