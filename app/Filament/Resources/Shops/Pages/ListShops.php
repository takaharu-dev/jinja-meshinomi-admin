<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Pages;

use App\Filament\Resources\Shops\ShopResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Size;

class ListShops extends ListRecords
{
    protected static string $resource = ShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 一覧でいちばん大事な操作なので、大きく出す
            CreateAction::make()->size(Size::Large),
        ];
    }
}
