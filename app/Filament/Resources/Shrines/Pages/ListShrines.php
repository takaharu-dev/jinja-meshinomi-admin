<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shrines\Pages;

use App\Filament\Resources\Shrines\ShrineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Size;
use Illuminate\Database\Eloquent\Builder;

class ListShrines extends ListRecords
{
    protected static string $resource = ShrineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 一覧でいちばん大事な操作なので、大きく出す
            CreateAction::make()->size(Size::Large),
        ];
    }

    /**
     * 一覧の上のタブ。よく見る絞り込みを1クリックで。
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('すべて'),
            'published' => Tab::make('公開中')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', true)),
            'draft' => Tab::make('非公開')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false)),
            'no_photos' => Tab::make('写真がない')
                ->modifyQueryUsing(fn (Builder $query) => $query->doesntHave('photos')),
        ];
    }
}
