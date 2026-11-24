<?php

declare(strict_types=1);

namespace App\Filament\Resources\Features\Pages;

use App\Filament\Resources\Features\FeatureResource;
use App\Filament\Resources\Features\Schemas\FeatureForm;
use Filament\Resources\Pages\CreateRecord;

class CreateFeature extends CreateRecord
{
    protected static string $resource = FeatureResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return FeatureForm::prepareForSave($data);
    }
}
