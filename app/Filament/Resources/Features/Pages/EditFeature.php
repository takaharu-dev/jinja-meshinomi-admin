<?php

declare(strict_types=1);

namespace App\Filament\Resources\Features\Pages;

use App\Filament\Resources\Features\FeatureResource;
use App\Filament\Resources\Features\Schemas\FeatureForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFeature extends EditRecord
{
    protected static string $resource = FeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return FeatureForm::prepareForSave($data);
    }
}
