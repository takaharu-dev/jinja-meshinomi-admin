<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\AreaGroups\AreaGroupResource;
use App\Filament\Resources\Areas\AreaResource;
use App\Filament\Resources\Features\FeatureResource;
use App\Filament\Resources\Shops\ShopResource;
use App\Filament\Resources\Shrines\ShrineResource;
use App\Filament\Resources\Stations\StationResource;
use App\Filament\Resources\VisitLogs\VisitLogResource;
use App\Filament\Resources\Worries\WorryResource;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;

/**
 * ダッシュボードの「使い方」。登録の順番と、各画面への入り口（一覧・登録）と件数を並べる。
 */
class GuideWidget extends Widget
{
    protected string $view = 'filament.widgets.guide';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -10;

    /**
     * 登録の順番。前のステップのデータを、あとのステップで選んで使う。
     *
     * @return list<array{title: string, description: string, resources: list<array{label: string, description: string, icon: mixed, count: string, listUrl: string, createUrl: string}>}>
     */
    public function getSteps(): array
    {
        return [
            [
                'title' => '下準備（最初に1回）',
                'description' => '神社を登録するときに選ぶ項目です。足りないものがあれば追加します。',
                'resources' => [
                    $this->card(WorryResource::class, '「仕事・出世」など、神社を探すときの悩み'),
                    $this->card(AreaGroupResource::class, '「都心」など、エリアをまとめる見出し'),
                    $this->card(AreaResource::class, '「港区」など、神社がある地域'),
                    $this->card(StationResource::class, '神社の最寄り駅'),
                ],
            ],
            [
                'title' => 'お店と神社を登録する',
                'description' => '神社を保存すると編集画面に移ります。画面の下のタブで、悩み・最寄り駅・参拝後のお店・写真を追加します。',
                'resources' => [
                    $this->card(ShrineResource::class, '名前・住所・紹介文・地酒。悩み・駅・お店・写真は保存後にタブで追加', publishable: true),
                    $this->card(ShopResource::class, '参拝後に寄るお店。神社の画面からその場で作ることもできる'),
                ],
            ],
            [
                'title' => '記事を書く',
                'description' => '登録した神社・お店を選んで書きます。',
                'resources' => [
                    $this->card(VisitLogResource::class, '実際に参拝した日の記録。写真は保存後にタブで追加', publishable: true),
                    $this->card(FeatureResource::class, '条件（悩み・エリアなど）に合う神社が自動で並ぶまとめ記事', publishable: true),
                ],
            ],
        ];
    }

    /**
     * @param  class-string<resource>  $resource
     * @return array{label: string, description: string, icon: mixed, count: string, listUrl: string, createUrl: string}
     */
    private function card(string $resource, string $description, bool $publishable = false): array
    {
        $query = $resource::getModel()::query();
        $total = $query->count();
        $published = $publishable ? (clone $query)->where('is_published', true)->count() : null;

        return [
            'label' => $resource::getModelLabel(),
            'description' => $description,
            'icon' => $resource::getNavigationIcon(),
            'count' => $total.'件'.(is_null($published) ? '' : "（公開 {$published}）"),
            'listUrl' => $resource::getUrl('index'),
            'createUrl' => $resource::getUrl('create'),
        ];
    }
}
