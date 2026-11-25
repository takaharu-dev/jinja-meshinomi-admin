<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Prefecture;
use App\Models\Shrine;
use App\Models\Station;
use App\Models\Worry;
use Illuminate\Database\Seeder;

/**
 * 神社と、悩み・最寄り駅の紐づけ。本体の backend/data.go の shrines から移したもの。
 *
 * - 本体の仮置きのお店（「店名が入ります」「徒歩◯分」）は移さない。本物のお店は管理画面で入れる
 * - 運営者が参拝したことのない神社は、非公開（is_published = false）で入れる
 * - スラッグは「正式名のローマ字をハイフンでつなぐ」ルールにそろえた。本体の ID からの対応は LEGACY_IDS
 *   （本体の古い URL /shrines/{id} から新しい URL への 301 リダイレクトに使う）
 * - 何度流しても同じ結果になるよう、エリアとスラッグで探して上書きする
 *
 * MasterSeeder のあとに流す。
 */
class ShrineSeeder extends Seeder
{
    /**
     * 本体の新着順のまま。stations は先頭が最寄り駅（walk はその駅からの徒歩分数）。
     *
     * @var list<array<string, mixed>>
     */
    private const array SHRINES = [
        [
            'slug' => 'atago-jinja', 'name' => '愛宕神社', 'kana' => 'あたごじんじゃ', 'area' => 'minato', 'worry' => 'career',
            'address' => '東京都港区愛宕1-5-3',
            'catch' => '86段の「出世の石段」を登って、仕事のモヤモヤを切り替える',
            'benefit' => '出世・仕事運', 'highlight' => '86段の急な石段と、山の上の静かな境内',
            'after_visit' => '近くの蕎麦屋で一杯', 'popularity' => 98, 'goshuin' => true,
            'stations' => ['神谷町駅', '虎ノ門ヒルズ駅'], 'walk' => 5, 'published' => true,
        ],
        [
            'slug' => 'tokyo-daijingu', 'name' => '東京大神宮', 'kana' => 'とうきょうだいじんぐう', 'area' => 'chiyoda', 'worry' => 'love',
            'address' => '東京都千代田区富士見2-4-1',
            'catch' => '「東京のお伊勢さま」で良縁祈願。参拝後は神楽坂までひと歩き',
            'benefit' => '縁結び', 'highlight' => null,
            'after_visit' => '神楽坂の小料理屋で日本酒', 'popularity' => 95, 'goshuin' => true,
            'stations' => ['飯田橋駅'], 'walk' => 5, 'published' => true,
        ],
        [
            'slug' => 'hie-jinja', 'name' => '日枝神社', 'kana' => 'ひえじんじゃ', 'area' => 'chiyoda', 'worry' => 'career',
            'address' => '東京都千代田区永田町2-10-5',
            'catch' => '「山王さん」に仕事運と成長を願って、気持ちを立て直す',
            'benefit' => '仕事運・成長', 'highlight' => null,
            'after_visit' => '赤坂で軽く一杯', 'popularity' => 80, 'goshuin' => true,
            'stations' => ['溜池山王駅', '赤坂駅'], 'walk' => 3, 'published' => true,
        ],
        [
            'slug' => 'kotohiragu', 'name' => '金刀比羅宮', 'kana' => 'ことひらぐう', 'area' => 'minato', 'worry' => 'career',
            'address' => '東京都港区虎ノ門1-2-7',
            'catch' => '虎ノ門のビル街で、運と流れを変えるお参り',
            'benefit' => '開運・招福', 'highlight' => null,
            'after_visit' => '虎ノ門の居酒屋で乾杯', 'popularity' => 60, 'goshuin' => false,
            'stations' => ['虎ノ門駅'], 'walk' => 1, 'published' => true,
        ],
        [
            'slug' => 'akagi-jinja', 'name' => '赤城神社', 'kana' => 'あかぎじんじゃ', 'area' => 'shinjuku', 'worry' => 'career',
            'address' => '東京都新宿区赤城元町1-10',
            'catch' => '仕事や産業の発展を見守る、神楽坂のモダンな神社',
            'benefit' => '仕事・学業', 'highlight' => null,
            'after_visit' => '神楽坂の和食店で日本酒', 'popularity' => 70, 'goshuin' => true,
            'stations' => ['神楽坂駅'], 'walk' => 1, 'published' => true,
        ],
        [
            'slug' => 'koami-jinja', 'name' => '小網神社', 'kana' => 'こあみじんじゃ', 'area' => 'chuo', 'worry' => 'money',
            'address' => '東京都中央区日本橋小網町16-23',
            'catch' => '「強運厄除け」で知られる、日本橋の小さな神社',
            'benefit' => '金運・厄除け', 'highlight' => null,
            'after_visit' => '人形町で甘味と地酒', 'popularity' => 88, 'goshuin' => true,
            'stations' => ['人形町駅'], 'walk' => 7, 'published' => true,
        ],
        // 運営者がまだ参拝していないので非公開。行ったら写真と一緒に公開する
        [
            'slug' => 'asagaya-shinmeigu', 'name' => '阿佐ヶ谷神明宮', 'kana' => 'あさがやしんめいぐう', 'area' => 'suginami', 'worry' => 'yakuyoke',
            'address' => '東京都杉並区阿佐谷北1-25-5',
            'catch' => '全国でも珍しい「八難除」で、悪い流れを心機一転',
            'benefit' => '厄除け・開運', 'highlight' => null,
            'after_visit' => '阿佐ヶ谷の酒場で乾杯', 'popularity' => 90, 'goshuin' => true,
            'stations' => ['阿佐ケ谷駅'], 'walk' => 2, 'published' => false,
        ],
        [
            'slug' => 'izumo-taisha-tokyo-bunshi', 'name' => '出雲大社東京分祠', 'kana' => 'いずもおおやしろとうきょうぶんし', 'area' => 'minato', 'worry' => 'love',
            'address' => '東京都港区六本木7-18-5',
            'catch' => '六本木のビル群の中で、良いご縁を願う',
            'benefit' => '縁結び', 'highlight' => null,
            'after_visit' => '六本木の路地裏で一杯', 'popularity' => 75, 'goshuin' => false,
            'stations' => ['六本木駅'], 'walk' => 1, 'published' => false,
        ],
    ];

    /** 本体の古い ID => 新しいスラッグ */
    public const array LEGACY_IDS = [
        'atago' => 'atago-jinja',
        'tokyo-daijingu' => 'tokyo-daijingu',
        'hie' => 'hie-jinja',
        'kotohiragu' => 'kotohiragu',
        'akagi' => 'akagi-jinja',
        'koami' => 'koami-jinja',
        'asagaya-shinmeigu' => 'asagaya-shinmeigu',
        'izumo-tokyo' => 'izumo-taisha-tokyo-bunshi',
    ];

    public function run(): void
    {
        $tokyo = Prefecture::query()->where('slug', 'tokyo')->sole();

        foreach (self::SHRINES as $data) {
            $area = Area::query()->where('prefecture_id', $tokyo->id)->where('slug', $data['area'])->sole();

            $shrine = Shrine::query()->updateOrCreate(['area_id' => $area->id, 'slug' => $data['slug']], [
                'name' => $data['name'],
                'kana' => $data['kana'],
                'address' => $data['address'],
                'catch' => $data['catch'],
                'benefit' => $data['benefit'],
                'highlight' => $data['highlight'],
                'after_visit' => $data['after_visit'],
                'popularity' => $data['popularity'],
                'has_goshuin' => $data['goshuin'],
                'is_published' => $data['published'],
            ]);

            $shrine->worries()->syncWithoutDetaching([Worry::query()->where('slug', $data['worry'])->sole()->id]);
            $shrine->setPrimaryWorry(Worry::query()->where('slug', $data['worry'])->sole());

            foreach ($data['stations'] as $i => $name) {
                // 駅のエリアは神社のエリアにする（本体は駅ごとのエリアを持っていない）
                $station = Station::query()->firstOrCreate(['name' => $name], ['area_id' => $area->id]);
                $shrine->stations()->syncWithoutDetaching([$station->id => ['sort_order' => $i]]);

                if ($i === 0) {
                    $shrine->setNearestStation($station, $data['walk']);
                }
            }
        }
    }
}
