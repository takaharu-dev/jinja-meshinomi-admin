<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Area;
use App\Models\AreaGroup;
use App\Models\Prefecture;
use App\Models\Worry;
use Illuminate\Database\Seeder;

/**
 * 悩み・都道府県・エリアグループ・エリア。本体の backend/data.go（worries・worryDays・areas）と
 * backend/top.go（エリアグループ）から移したもの。
 *
 * 何度流しても同じ結果になるよう、スラッグ・名前で探して上書きする。
 */
class MasterSeeder extends Seeder
{
    /** slug => [表示名, アイコンの1文字, こんな日に行きたい] */
    private const array WORRIES = [
        'career' => ['仕事・出世', '出', [
            '上司や評価のことで、気持ちが沈んでいる日',
            '異動や転職など、仕事の節目を迎える前',
            '「このままでいいのかな」と、もやもやが晴れない週末',
        ]],
        'love' => ['恋愛・縁結び', '縁', [
            'なかなか良い出会いがなくて、少し疲れてしまった日',
            '大切な人との関係を、もう一歩進めたいとき',
            '新しいご縁に向けて、気持ちを整えたい週末',
        ]],
        'enkiri' => ['縁切り', '切', [
            '断ち切りたい習慣や人間関係があるとき',
            '新しいことを始める前に、気持ちをリセットしたい日',
        ]],
        'money' => ['金運', '金', [
            'お金のことで、不安がふくらんでいる日',
            '新しい仕事や副業を始める前',
            '「流れを変えたい」と思った週末',
        ]],
        'yakuyoke' => ['厄除け', '厄', [
            '厄年を迎えて、なんとなく落ち着かないとき',
            '悪いことが続いて、流れを変えたい日',
            '新しい年度や季節の変わり目に、気持ちを新たにしたいとき',
        ]],
        'health' => ['健康', '健', [
            '自分や家族の体のことが気がかりな日',
            '忙しさが続いて、心と体を休めたいとき',
        ]],
        'victory' => ['勝負運', '勝', [
            '試験や大事なプレゼンを控えているとき',
            'ここ一番の勝負の前に、気持ちを引き締めたい日',
        ]],
        'relations' => ['人間関係', '和', [
            '職場や友人との関係に、少し疲れてしまった日',
            '新しい環境で、良い人間関係を築きたいとき',
        ]],
    ];

    /** 本体のエリアは東京都だけなので、都道府県も東京都だけ入れる。ほかの道府県は管理画面で足す */
    private const array PREFECTURES = ['tokyo' => '東京都'];

    /** エリアグループ名 => [エリアの slug => 表示名]。すべて東京都 */
    private const array AREAS = [
        '都心' => ['chiyoda' => '千代田区', 'chuo' => '中央区', 'minato' => '港区'],
        '副都心' => ['shinjuku' => '新宿区', 'shibuya' => '渋谷区', 'toshima' => '豊島区'],
        '下町' => ['taito' => '台東区', 'sumida' => '墨田区', 'koto' => '江東区'],
        '西エリア' => ['suginami' => '杉並区', 'setagaya' => '世田谷区', 'musashino' => '武蔵野市'],
    ];

    public function run(): void
    {
        $this->seedWorries();
        $this->seedAreas();
    }

    private function seedWorries(): void
    {
        $order = 0;
        foreach (self::WORRIES as $slug => [$label, $icon, $scenes]) {
            $worry = Worry::query()->updateOrCreate(
                ['slug' => $slug],
                ['label' => $label, 'icon' => $icon, 'sort_order' => $order++],
            );

            // 「こんな日に行きたい」は丸ごと入れ直す（並び順も文章どおりにする）
            $worry->scenes()->delete();
            foreach ($scenes as $i => $text) {
                $worry->scenes()->create(['text' => $text, 'sort_order' => $i]);
            }
        }
    }

    private function seedAreas(): void
    {
        $order = 0;
        foreach (self::PREFECTURES as $slug => $label) {
            Prefecture::query()->updateOrCreate(['slug' => $slug], ['label' => $label, 'sort_order' => $order++]);
        }
        $tokyo = Prefecture::query()->where('slug', 'tokyo')->sole();

        $groupOrder = 0;
        $areaOrder = 0;
        foreach (self::AREAS as $groupName => $areas) {
            $group = AreaGroup::query()->updateOrCreate(['name' => $groupName], ['sort_order' => $groupOrder++]);

            foreach ($areas as $slug => $label) {
                Area::query()->updateOrCreate(
                    ['prefecture_id' => $tokyo->id, 'slug' => $slug],
                    ['label' => $label, 'area_group_id' => $group->id, 'sort_order' => $areaOrder++],
                );
            }
        }
    }
}
