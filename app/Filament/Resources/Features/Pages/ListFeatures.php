<?php

declare(strict_types=1);

namespace App\Filament\Resources\Features\Pages;

use App\Filament\Resources\Features\FeatureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Size;

class ListFeatures extends ListRecords
{
    protected static string $resource = FeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 一覧でいちばん大事な操作なので、大きく出す
            CreateAction::make()->size(Size::Large),
        ];
    }
}
